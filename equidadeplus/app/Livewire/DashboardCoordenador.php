<?php

namespace App\Livewire;

use App\Models\Atendimento;
use App\Models\Evolucao;
use App\Models\Avaliacao;
use App\Models\Paciente;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class DashboardCoordenador extends Component
{
    public $atendimentosHoje;
    public $evolucoesPendentes;
    public $avaliacoesRascunho;
    public $totalPacientes;
    public $totalProfissionais;
    public $produtividadeProfissionais;

    public function mount()
    {
        // Verifica permissão - Coordenador e Admin
        if (!Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Apenas coordenadores e administradores podem acessar este dashboard.');
        }

        $this->carregarDados();
    }

    public function carregarDados()
    {
        $user = Auth::user();
        $unidadeSelecionada = session('unidade_selecionada');

        // Atendimentos de hoje
        $queryAtendimentos = Atendimento::whereDate('data_hora_inicio', today())
            ->with(['paciente', 'profissional', 'sala']);

        if ($unidadeSelecionada) {
            $queryAtendimentos->whereHas('sala', function ($q) use ($unidadeSelecionada) {
                $q->where('unidade_id', $unidadeSelecionada);
            });
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryAtendimentos->whereHas('sala', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_id', $unidadeIds);
            });
        }

        $this->atendimentosHoje = $queryAtendimentos->orderBy('data_hora_inicio')->get();

        // Evoluções pendentes
        $queryEvolucoes = Evolucao::where('status', 'Rascunho')
            ->whereNull('evolucao_pai_id')
            ->with(['paciente', 'profissional']);

        if ($unidadeSelecionada) {
            $queryEvolucoes->whereHas('paciente', function ($q) use ($unidadeSelecionada) {
                $q->where('unidade_padrao_id', $unidadeSelecionada);
            });
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryEvolucoes->whereHas('paciente', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_padrao_id', $unidadeIds);
            });
        }

        $this->evolucoesPendentes = $queryEvolucoes->orderBy('created_at', 'desc')->get();

        // Avaliações em rascunho
        $queryAvaliacoes = Avaliacao::where('status', 'Rascunho')
            ->with(['paciente', 'template', 'profissional']);

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

        $this->avaliacoesRascunho = $queryAvaliacoes->orderBy('created_at', 'desc')->get();

        // Total de pacientes
        $queryPacientes = Paciente::query();
        if ($unidadeSelecionada) {
            $queryPacientes->where('unidade_padrao_id', $unidadeSelecionada);
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryPacientes->whereIn('unidade_padrao_id', $unidadeIds);
        }
        $this->totalPacientes = $queryPacientes->count();

        // Total de profissionais
        $queryProfissionais = User::whereHas('roles', function ($q) {
            $q->where('name', 'Profissional');
        });
        
        if ($unidadeSelecionada) {
            $queryProfissionais->whereHas('unidades', function ($q) use ($unidadeSelecionada) {
                $q->where('unidades.id', $unidadeSelecionada);
            });
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryProfissionais->whereHas('unidades', function ($q) use ($unidadeIds) {
                $q->whereIn('unidades.id', $unidadeIds);
            });
        }
        $this->totalProfissionais = $queryProfissionais->count();

        // Produtividade por profissional (últimos 30 dias)
        $queryProdutividade = Atendimento::select('user_id', DB::raw('count(*) as total'))
            ->where('status', 'Concluído')
            ->whereDate('data_hora_inicio', '>=', now()->subDays(30))
            ->groupBy('user_id');

        if ($unidadeSelecionada) {
            $queryProdutividade->whereHas('sala', function ($q) use ($unidadeSelecionada) {
                $q->where('unidade_id', $unidadeSelecionada);
            });
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryProdutividade->whereHas('sala', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_id', $unidadeIds);
            });
        }

        $this->produtividadeProfissionais = $queryProdutividade->with('profissional')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-coordenador');
    }
}

