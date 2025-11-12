<?php

namespace App\Livewire;

use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\Documento;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class DashboardSecretaria extends Component
{
    public $atendimentosHoje;
    public $pacientesAguardando;
    public $documentosPendentes;
    public $totalPacientes;
    public $pacientesNovosMes;

    public function mount()
    {
        // Apenas Secretaria pode acessar
        if (!Auth::user()->hasRole('Secretaria') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Apenas secretaria pode acessar este dashboard.');
        }

        $this->carregarDados();
    }

    public function carregarDados()
    {
        $user = Auth::user();
        $unidadeSelecionada = session('unidade_selecionada');

        // Atendimentos de hoje (todos os profissionais)
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

        // Pacientes aguardando cadastro (sem dados completos ou sem responsável)
        $queryPacientes = Paciente::query();
        
        if ($unidadeSelecionada) {
            $queryPacientes->where('unidade_padrao_id', $unidadeSelecionada);
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryPacientes->whereIn('unidade_padrao_id', $unidadeIds);
        }

        // Pacientes sem responsável ou sem dados completos
        $this->pacientesAguardando = $queryPacientes->whereDoesntHave('responsaveis')
            ->orWhere(function ($q) {
                $q->whereNull('email_principal')
                  ->orWhereNull('telefone_principal');
            })
            ->where('status', 'Ativo')
            ->count();

        // Documentos pendentes (pacientes sem documentos recentes)
        $queryDocumentos = Paciente::query();
        
        if ($unidadeSelecionada) {
            $queryDocumentos->where('unidade_padrao_id', $unidadeSelecionada);
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryDocumentos->whereIn('unidade_padrao_id', $unidadeIds);
        }

        $this->documentosPendentes = $queryDocumentos->whereHas('atendimentos', function ($q) {
            $q->where('status', 'Concluído')
              ->whereDate('data_hora_inicio', '>=', now()->subDays(30));
        })->whereDoesntHave('documentos', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(30));
        })->count();

        // Total de pacientes
        $queryTotal = Paciente::query();
        if ($unidadeSelecionada) {
            $queryTotal->where('unidade_padrao_id', $unidadeSelecionada);
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryTotal->whereIn('unidade_padrao_id', $unidadeIds);
        }
        $this->totalPacientes = $queryTotal->count();

        // Pacientes novos no mês
        $queryNovos = Paciente::query();
        if ($unidadeSelecionada) {
            $queryNovos->where('unidade_padrao_id', $unidadeSelecionada);
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryNovos->whereIn('unidade_padrao_id', $unidadeIds);
        }
        $this->pacientesNovosMes = $queryNovos->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard-secretaria');
    }
}

