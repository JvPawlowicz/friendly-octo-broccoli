<?php

namespace App\Livewire;

use App\Models\Avaliacao;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class AvaliacoesUnidade extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $profissionalFilter = '';
    public $pacienteFilter = '';
    public $dataInicio = '';
    public $dataFim = '';

    public function mount()
    {
        // Verifica permissão - Coordenador e Admin
        if (!Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para visualizar avaliações da unidade.');
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

    public function render()
    {
        $user = Auth::user();
        $unidadeSelecionada = session('unidade_selecionada');

        $query = Avaliacao::query()
            ->with(['paciente', 'template', 'profissional']);

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

        // Filtro por profissional
        if ($this->profissionalFilter) {
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

        $avaliacoes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estatísticas
        $queryStats = Avaliacao::query();
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

        $total = (clone $queryStats)->count();
        $rascunho = (clone $queryStats)->where('status', 'Rascunho')->count();
        $finalizadas = (clone $queryStats)->where('status', 'Finalizado')->count();

        // Lista de profissionais para filtro
        $profissionais = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'Profissional');
        })->orderBy('name')->get();

        // Lista de pacientes para filtro
        $pacientes = \App\Models\Paciente::query();
        if ($unidadeSelecionada) {
            $pacientes->where('unidade_padrao_id', $unidadeSelecionada);
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $pacientes->whereIn('unidade_padrao_id', $unidadeIds);
        }
        $pacientes = $pacientes->orderBy('nome_completo')->get();

        return view('livewire.avaliacoes-unidade', [
            'avaliacoes' => $avaliacoes,
            'total' => $total,
            'rascunho' => $rascunho,
            'finalizadas' => $finalizadas,
            'profissionais' => $profissionais,
            'pacientes' => $pacientes,
        ]);
    }
}

