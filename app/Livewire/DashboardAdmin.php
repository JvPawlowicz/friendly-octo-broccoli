<?php

namespace App\Livewire;

use App\Models\Atendimento;
use App\Models\Evolucao;
use App\Models\Avaliacao;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Unidade;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class DashboardAdmin extends Component
{
    public $totalUnidades;
    public $totalPacientes;
    public $totalProfissionais;
    public $totalAtendimentosMes;
    public $evolucoesPendentes;
    public $avaliacoesRascunho;
    public $produtividadePorUnidade;
    public $atendimentosHoje;

    public function mount()
    {
        // Apenas Admin pode acessar
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Apenas administradores podem acessar este dashboard.');
        }

        $this->carregarDados();
    }

    public function carregarDados()
    {
        // Totais gerais
        $this->totalUnidades = Unidade::count();
        $this->totalPacientes = Paciente::count();
        $this->totalProfissionais = User::whereHas('roles', function ($q) {
            $q->where('name', 'Profissional');
        })->count();

        // Atendimentos do mês
        $this->totalAtendimentosMes = Atendimento::whereMonth('data_hora_inicio', now()->month)
            ->whereYear('data_hora_inicio', now()->year)
            ->where('status', 'Concluído')
            ->count();

        // Evoluções pendentes (todas as unidades)
        $this->evolucoesPendentes = Evolucao::where('status', 'Rascunho')
            ->whereNull('evolucao_pai_id')
            ->with(['paciente', 'profissional'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Avaliações em rascunho (todas as unidades)
        $this->avaliacoesRascunho = Avaliacao::where('status', 'Rascunho')
            ->with(['paciente', 'template', 'profissional'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Produtividade por unidade (últimos 30 dias)
        $this->produtividadePorUnidade = Unidade::withCount(['salas' => function ($q) {
            $q->whereHas('atendimentos', function ($q2) {
                $q2->where('status', 'Concluído')
                   ->whereDate('data_hora_inicio', '>=', now()->subDays(30));
            });
        }])->get()->map(function ($unidade) {
            $atendimentos = Atendimento::whereHas('sala', function ($q) use ($unidade) {
                $q->where('unidade_id', $unidade->id);
            })->where('status', 'Concluído')
              ->whereDate('data_hora_inicio', '>=', now()->subDays(30))
              ->count();
            
            return [
                'unidade' => $unidade,
                'atendimentos' => $atendimentos,
            ];
        })->sortByDesc('atendimentos')->take(5);

        // Atendimentos de hoje (todas as unidades)
        $this->atendimentosHoje = Atendimento::whereDate('data_hora_inicio', today())
            ->with(['paciente', 'profissional', 'sala.unidade'])
            ->orderBy('data_hora_inicio')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-admin');
    }
}

