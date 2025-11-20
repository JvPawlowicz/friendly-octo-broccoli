<?php

namespace App\Livewire;

use App\Models\Atendimento;
use App\Models\Evolucao;
use App\Models\Avaliacao;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Unidade;
use App\Models\Documento;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    // Dados compartilhados
    public $atendimentosHoje;
    public $evolucoesPendentes;
    public $avaliacoesRascunho;
    
    // Dados específicos por role
    public $totalPacientes;
    public $totalProfissionais;
    public $totalUnidades;
    public $totalAtendimentosMes;
    public $pacientesAguardando;
    public $documentosPendentes;
    public $pacientesNovosMes;
    public $produtividadePorUnidade;
    public $produtividadeProfissionais;

    public function mount()
    {
        $this->carregarDados();
    }

    public function carregarDados()
    {
        $user = Auth::user();
        $unidadeSelecionada = session('unidade_selecionada');
        $isAdmin = $user->hasRole('Admin');
        $isCoordenador = $user->hasRole('Coordenador');
        $isSecretaria = $user->hasRole('Secretaria');
        $isProfissional = $user->hasRole('Profissional');

        // Escopo de unidade
        $unidadeIds = $isAdmin ? null : ($unidadeSelecionada 
            ? [$unidadeSelecionada] 
            : $user->unidades->pluck('id')->toArray());

        // Atendimentos de hoje (todos os roles)
        $this->atendimentosHoje = $this->queryAtendimentos($unidadeIds, $user, $isAdmin, $isProfissional)
            ->whereDate('data_hora_inicio', today())
            ->with(['paciente', 'profissional', 'sala'])
            ->orderBy('data_hora_inicio')
            ->limit(10)
            ->get();

        // Evoluções pendentes (exceto secretaria)
        if (!$isSecretaria) {
            $this->evolucoesPendentes = $this->queryEvolucoes($unidadeIds, $user, $isAdmin, $isProfissional)
                ->where('status', 'Rascunho')
                ->whereNull('evolucao_pai_id')
                ->with(['paciente', 'profissional'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Avaliações em rascunho (exceto secretaria)
        if (!$isSecretaria) {
            $this->avaliacoesRascunho = $this->queryAvaliacoes($unidadeIds, $user, $isAdmin, $isProfissional)
                ->where('status', 'Rascunho')
                ->with(['paciente', 'template', 'profissional'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Dados específicos por role
        if ($isAdmin) {
            $this->carregarDadosAdmin();
        } elseif ($isCoordenador) {
            $this->carregarDadosCoordenador($unidadeIds);
        } elseif ($isSecretaria) {
            $this->carregarDadosSecretaria($unidadeIds);
        } elseif ($isProfissional) {
            $this->carregarDadosProfissional($user);
        }
    }

    protected function queryAtendimentos($unidadeIds, $user, $isAdmin, $isProfissional)
    {
        $query = Atendimento::query();

        if ($isProfissional && !$isAdmin) {
            $query->where('user_id', $user->id);
        } elseif (!$isAdmin && $unidadeIds) {
            $query->whereHas('sala', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_id', $unidadeIds);
            });
        }

        return $query;
    }

    protected function queryEvolucoes($unidadeIds, $user, $isAdmin, $isProfissional)
    {
        $query = Evolucao::query();

        if ($isProfissional && !$isAdmin) {
            $query->where('user_id', $user->id);
        } elseif (!$isAdmin && $unidadeIds) {
            $query->whereHas('paciente', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_padrao_id', $unidadeIds);
            });
        }

        return $query;
    }

    protected function queryAvaliacoes($unidadeIds, $user, $isAdmin, $isProfissional)
    {
        $query = Avaliacao::query();

        if ($isProfissional && !$isAdmin) {
            $query->where('user_id', $user->id);
        } elseif (!$isAdmin && $unidadeIds) {
            $query->whereHas('paciente', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_padrao_id', $unidadeIds);
            });
        }

        return $query;
    }

    protected function carregarDadosAdmin()
    {
        $this->totalUnidades = Unidade::count();
        $this->totalPacientes = Paciente::count();
        $this->totalProfissionais = User::whereHas('roles', function ($q) {
            $q->where('name', 'Profissional');
        })->count();

        $this->totalAtendimentosMes = Atendimento::contaveis()
            ->whereMonth('data_hora_inicio', now()->month)
            ->whereYear('data_hora_inicio', now()->year)
            ->where('status', 'Concluído')
            ->count();

        $this->produtividadePorUnidade = Unidade::get()->map(function ($unidade) {
            $atendimentos = Atendimento::contaveis()
                ->whereHas('sala', function ($q) use ($unidade) {
                    $q->where('unidade_id', $unidade->id);
                })->where('status', 'Concluído')
                  ->whereDate('data_hora_inicio', '>=', now()->subDays(30))
                  ->count();
            
            return [
                'unidade' => $unidade,
                'atendimentos' => $atendimentos,
            ];
        })->sortByDesc('atendimentos')->take(5);
    }

    protected function carregarDadosCoordenador($unidadeIds)
    {
        $queryPacientes = Paciente::query();
        if ($unidadeIds) {
            $queryPacientes->whereIn('unidade_padrao_id', $unidadeIds);
        }
        $this->totalPacientes = $queryPacientes->count();

        $queryProfissionais = User::whereHas('roles', function ($q) {
            $q->where('name', 'Profissional');
        });
        if ($unidadeIds) {
            $queryProfissionais->whereHas('unidades', function ($q) use ($unidadeIds) {
                $q->whereIn('unidades.id', $unidadeIds);
            });
        }
        $this->totalProfissionais = $queryProfissionais->count();

        $queryProdutividade = Atendimento::contaveis()
            ->select('user_id', DB::raw('count(*) as total'))
            ->where('status', 'Concluído')
            ->whereDate('data_hora_inicio', '>=', now()->subDays(30))
            ->groupBy('user_id');

        if ($unidadeIds) {
            $queryProdutividade->whereHas('sala', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_id', $unidadeIds);
            });
        }

        $this->produtividadeProfissionais = $queryProdutividade->with('profissional')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    protected function carregarDadosSecretaria($unidadeIds)
    {
        $queryPacientes = Paciente::query();
        if ($unidadeIds) {
            $queryPacientes->whereIn('unidade_padrao_id', $unidadeIds);
        }

        $this->pacientesAguardando = $queryPacientes->whereDoesntHave('responsaveis')
            ->orWhere(function ($q) {
                $q->whereNull('email_principal')
                  ->orWhereNull('telefone_principal');
            })
            ->where('status', 'Ativo')
            ->count();

        $this->documentosPendentes = $queryPacientes->whereHas('atendimentos', function ($q) {
            $q->where('status', 'Concluído')
              ->whereDate('data_hora_inicio', '>=', now()->subDays(30));
        })->whereDoesntHave('documentos', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(30));
        })->count();

        $this->totalPacientes = $queryPacientes->count();

        $this->pacientesNovosMes = $queryPacientes->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    protected function carregarDadosProfissional($user)
    {
        // Dados mínimos para profissional - foco em suas pendências
        $this->totalPacientes = Paciente::whereHas('atendimentos', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->distinct()->count();
    }

    public function render()
    {
        $user = Auth::user();
        $role = 'profissional';
        
        if ($user->hasRole('Admin')) {
            $role = 'admin';
        } elseif ($user->hasRole('Coordenador')) {
            $role = 'coordenador';
        } elseif ($user->hasRole('Secretaria')) {
            $role = 'secretaria';
        }

        return view('livewire.dashboard', ['role' => $role]);
    }
}


