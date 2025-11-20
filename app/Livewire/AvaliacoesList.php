<?php

namespace App\Livewire;

use App\Models\Avaliacao;
use App\Models\Paciente;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class AvaliacoesList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $profissionalFilter = '';
    public $pacienteFilter = '';
    public $dataInicio = '';
    public $dataFim = '';

    protected $scope = 'minhas'; // 'minhas' ou 'unidade'

    public function mount()
    {
        $user = Auth::user();
        
        // Define escopo baseado no role
        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $this->scope = 'minhas';
        } elseif ($user->hasAnyRole(['Admin', 'Coordenador'])) {
            $this->scope = 'unidade';
        } else {
            abort(403, 'Você não tem permissão para visualizar avaliações.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingProfissionalFilter()
    {
        $this->resetPage();
    }

    public function updatingPacienteFilter()
    {
        $this->resetPage();
    }

    public function limparFiltros()
    {
        $this->reset(['search', 'statusFilter', 'profissionalFilter', 'pacienteFilter', 'dataInicio', 'dataFim']);
        $this->resetPage();
    }

    protected function aplicarFiltros($query)
    {
        $user = Auth::user();
        $unidadeSelecionada = session('unidade_selecionada');

        // Escopo baseado no role
        if ($this->scope === 'minhas') {
            $query->where('user_id', $user->id);
        } else {
            // Filtro por unidade
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
                })->orWhereHas('template', function ($q2) {
                    $q2->where('nome_template', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Filtro por status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filtro por profissional (apenas para escopo unidade)
        if ($this->scope === 'unidade' && $this->profissionalFilter) {
            $query->where('user_id', $this->profissionalFilter);
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
    }

    protected function calcularEstatisticas()
    {
        $user = Auth::user();
        $unidadeSelecionada = session('unidade_selecionada');

        $queryStats = Avaliacao::query();

        if ($this->scope === 'minhas') {
            $queryStats->where('user_id', $user->id);
        } else {
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

        return [
            'total' => (clone $queryStats)->count(),
            'rascunho' => (clone $queryStats)->where('status', 'Rascunho')->count(),
            'finalizadas' => (clone $queryStats)->where('status', 'Finalizado')->count(),
        ];
    }

    public function render()
    {
        $user = Auth::user();

        $query = Avaliacao::query()
            ->with(['paciente', 'template', 'profissional']);

        $this->aplicarFiltros($query);

        $avaliacoes = $query->orderBy('created_at', 'desc')->paginate(15);

        $estatisticas = $this->calcularEstatisticas();

        // Lista de profissionais (apenas para escopo unidade)
        $profissionais = $this->scope === 'unidade'
            ? User::whereHas('roles', function ($q) {
                $q->where('name', 'Profissional');
            })->orderBy('name')->get()
            : collect();

        // Lista de pacientes
        $queryPacientes = Paciente::query();
        
        if ($this->scope === 'minhas') {
            $queryPacientes->whereHas('avaliacoes', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $queryPacientes->where('unidade_padrao_id', $unidadeSelecionada);
            } elseif (!$user->hasRole('Admin')) {
                $unidadeIds = $user->unidades->pluck('id')->toArray();
                $queryPacientes->whereIn('unidade_padrao_id', $unidadeIds);
            }
        }

        $pacientes = $queryPacientes->orderBy('nome_completo')->get();

        return view('livewire.avaliacoes-list', [
            'avaliacoes' => $avaliacoes,
            'estatisticas' => $estatisticas,
            'profissionais' => $profissionais,
            'pacientes' => $pacientes,
            'scope' => $this->scope,
        ]);
    }
}

