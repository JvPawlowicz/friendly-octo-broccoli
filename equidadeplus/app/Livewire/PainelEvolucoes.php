<?php

namespace App\Livewire;

use App\Models\Atendimento;
use App\Models\Evolucao;
use App\Models\Avaliacao;
use App\Models\UserPreference;
use App\Services\DashboardService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class PainelEvolucoes extends Component
{
    use \Livewire\WithPagination;

    public $atendimentosHoje;
    public $evolucoesPendentes;
    public $avaliacoesPendentes;
    public $abaAtiva = 'pendentes'; // 'pendentes' ou 'todas'
    public array $dashboardStats = [];
    public array $pendenciasDashboard = [];
    public string $notaRapida = '';
    public ?string $notaAtualizadaEm = null;
    
    // Filtros para aba "todas"
    public $search = '';
    public $statusFilter = '';
    public $pacienteFilter = '';
    public $dataInicio = '';
    public $dataFim = '';

    protected ?UserPreference $notaPreference = null;

    public function mount()
    {
        // Verifica permissão - Secretaria não tem acesso
        if (Auth::user()->hasRole('Secretaria')) {
            abort(403, 'Você não tem acesso a este módulo.');
        }
        
        $this->loadData();
        $this->carregarNotaRapida();
    }

    public function trocarAba($aba)
    {
        $this->abaAtiva = $aba;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPacienteFilter()
    {
        $this->resetPage();
    }

    public function limparFiltros()
    {
        $this->reset(['search', 'statusFilter', 'pacienteFilter', 'dataInicio', 'dataFim']);
        $this->resetPage();
    }

    /**
     * "Ouve" o evento disparado pelo FormEvolucao quando algo é salvo
     */
    #[On('evolucao-salva')]
    public function atualizarLista()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Atendimentos de hoje
        // Profissional: apenas seus atendimentos
        // Outros: todos da unidade selecionada
        $queryAtendimentos = Atendimento::query()
            ->whereDate('data_hora_inicio', today())
            ->whereIn('status', ['Agendado', 'Confirmado', 'Check-in'])
            ->with(['paciente', 'sala']);

        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            // Profissional vê apenas seus atendimentos
            $queryAtendimentos->where('user_id', $userId);
        } else {
            // Coordenador e Admin veem atendimentos da unidade
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $queryAtendimentos->whereHas('sala', function ($q) use ($unidadeSelecionada) {
                    $q->where('unidade_id', $unidadeSelecionada);
                });
            } elseif (!$user->hasRole('Admin')) {
                // Se não há unidade selecionada e não é Admin, filtra pelas unidades do usuário
                $unidadeIds = $user->unidades->pluck('id')->toArray();
                $queryAtendimentos->whereHas('sala', function ($q) use ($unidadeIds) {
                    $q->whereIn('unidade_id', $unidadeIds);
                });
            }
        }

        $this->atendimentosHoje = $queryAtendimentos->orderBy('data_hora_inicio')->get();
            
        // Evoluções pendentes
        // Profissional: apenas suas evoluções pendentes
        // Coordenador/Admin: todas as pendências da unidade
        $queryEvolucoes = Evolucao::where('status', 'Rascunho')
            ->with(['paciente', 'atendimento']);

        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            // Profissional vê apenas suas evoluções pendentes
            $queryEvolucoes->where('user_id', $userId);
        } else {
            // Coordenador e Admin veem evoluções pendentes da unidade
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $queryEvolucoes->whereHas('paciente', function ($q) use ($unidadeSelecionada) {
                    $q->where('unidade_padrao_id', $unidadeSelecionada);
                });
            } elseif (!$user->hasRole('Admin')) {
                // Se não há unidade selecionada e não é Admin, filtra pelas unidades do usuário
                $unidadeIds = $user->unidades->pluck('id')->toArray();
                $queryEvolucoes->whereHas('paciente', function ($q) use ($unidadeIds) {
                    $q->whereIn('unidade_padrao_id', $unidadeIds);
                });
            }
        }

        $this->evolucoesPendentes = $queryEvolucoes->orderBy('created_at', 'desc')->get();

        $queryAvaliacoes = Avaliacao::where('status', '!=', 'Finalizado')
            ->with(['paciente', 'template']);

        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $queryAvaliacoes->where('user_id', $userId);
        } else {
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $queryAvaliacoes->whereHas('paciente', function ($q) use ($unidadeSelecionada) {
                    $q->where('unidade_padrao_id', $unidadeSelecionada);
                });
            } elseif (!$user->hasRole('Admin')) {
                $unidadeIds = $user->unidades->pluck('id')->toArray();
                $queryAvaliacoes->whereHas('paciente', function ($q) use ($unidadeIds) {
                    $q->whereIn('unidade_padrao_id', $unidadeIds);
                });
            }
        }

        $this->avaliacoesPendentes = $queryAvaliacoes->orderBy('created_at', 'desc')->get();

        $this->pendenciasDashboard = [
            'evolucoes' => $this->evolucoesPendentes->count(),
            'avaliacoes' => $this->avaliacoesPendentes->count(),
        ];

        /** @var DashboardService $dashboard */
        $dashboard = app(DashboardService::class);
        $this->dashboardStats = $dashboard->metricsFor($user, session('unidade_selecionada'));
    }

    /**
     * Ação para abrir o formulário de evolução
     */
    public function editarEvolucao($evolucaoId)
    {
        return redirect()->route('app.evolucoes.edit', ['evolucaoId' => $evolucaoId]);
    }

    /**
     * Concluir atendimento (futuro - quando implementar Fluxo 1)
     */
    public function concluirAtendimento($atendimentoId)
    {
        $atendimento = Atendimento::findOrFail($atendimentoId);
        
        // Verifica permissão
        if ($atendimento->user_id !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            session()->flash('error', 'Você não tem permissão para concluir este atendimento.');
            return;
        }

        $atendimento->update(['status' => 'Concluído']);
        
        // Dispara o evento que cria a evolução pendente
        event(new \App\Events\AtendimentoConcluido($atendimento));
        
        $this->loadData();
        session()->flash('message', 'Atendimento concluído! Uma evolução pendente foi criada.');
    }

    public function salvarNotaRapida(): void
    {
        $this->validate([
            'notaRapida' => ['nullable', 'string', 'max:1000'],
        ]);

        $pref = Auth::user()->preferences()->updateOrCreate(
            ['key' => 'dashboard.nota'],
            ['value' => ['texto' => $this->notaRapida]]
        );

        $this->notaPreference = $pref;
        $this->notaAtualizadaEm = optional($pref->updated_at)->format('d/m/Y H:i');
        $this->dispatch('app:toast', message: 'Nota salva com sucesso.', type: 'success');
    }

    protected function carregarNotaRapida(): void
    {
        $pref = Auth::user()->preferences()->where('key', 'dashboard.nota')->first();
        if ($pref) {
            $this->notaPreference = $pref;
            $this->notaRapida = $pref->value['texto'] ?? '';
            $this->notaAtualizadaEm = optional($pref->updated_at)->format('d/m/Y H:i');
        }
    }

    public function getTodasEvolucoesProperty()
    {
        $user = Auth::user();
        $userId = $user->id;

        $query = Evolucao::query()
            ->whereNull('evolucao_pai_id') // Apenas evoluções pai, não adendos
            ->with(['paciente', 'atendimento', 'profissional']);

        // Filtro por usuário
        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $query->where('user_id', $userId);
        } else {
            // Coordenador e Admin veem evoluções da unidade
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $query->whereHas('paciente', function ($q) use ($unidadeSelecionada) {
                    $q->where('unidade_padrao_id', $unidadeSelecionada);
                });
            } elseif (!$user->hasRole('Admin')) {
                $unidadeIds = $user->unidades->pluck('id')->toArray();
                $query->whereHas('paciente', function ($q) use ($unidadeIds) {
                    $q->whereIn('unidade_padrao_id', $unidadeIds);
                });
            }
        }

        // Filtro de busca
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('paciente', function ($q2) {
                    $q2->where('nome_completo', 'like', '%' . $this->search . '%');
                })->orWhere('relato_clinico', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filtro por paciente
        if ($this->pacienteFilter) {
            $query->where('paciente_id', $this->pacienteFilter);
        }

        // Filtro por data
        if ($this->dataInicio) {
            $query->whereDate('created_at', '>=', $this->dataInicio);
        }
        if ($this->dataFim) {
            $query->whereDate('created_at', '<=', $this->dataFim);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function render()
    {
        $user = Auth::user();
        
        // Lista de pacientes para filtro
        $pacientes = \App\Models\Paciente::query();
        
        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $pacientes->whereHas('evolucoes', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $pacientes->where('unidade_padrao_id', $unidadeSelecionada);
            } elseif (!$user->hasRole('Admin')) {
                $unidadeIds = $user->unidades->pluck('id')->toArray();
                $pacientes->whereIn('unidade_padrao_id', $unidadeIds);
            }
        }
        
        $pacientes = $pacientes->orderBy('nome_completo')->get();

        // Estatísticas
        $queryStats = Evolucao::whereNull('evolucao_pai_id');
        
        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $queryStats->where('user_id', $user->id);
        } else {
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $queryStats->whereHas('paciente', function ($q) use ($unidadeSelecionada) {
                    $q->where('unidade_padrao_id', $unidadeSelecionada);
                });
            } elseif (!$user->hasRole('Admin')) {
                $unidadeIds = $user->unidades->pluck('id')->toArray();
                $queryStats->whereHas('paciente', function ($q) use ($unidadeIds) {
                    $q->whereIn('unidade_padrao_id', $unidadeIds);
                });
            }
        }

        $totalEvolucoes = $queryStats->count();
        $rascunho = (clone $queryStats)->where('status', 'Rascunho')->count();
        $finalizadas = (clone $queryStats)->where('status', 'Finalizado')->count();

        return view('livewire.painel-evolucoes', [
            'todasEvolucoes' => $this->abaAtiva === 'todas' ? $this->todasEvolucoes : null,
            'pacientes' => $pacientes,
            'totalEvolucoes' => $totalEvolucoes,
            'rascunho' => $rascunho,
            'finalizadas' => $finalizadas,
            'avaliacoesPendentes' => $this->avaliacoesPendentes,
        ])->layoutData([
            'dashboardStats' => $this->dashboardStats,
        ]);
    }
}
