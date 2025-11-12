<?php

namespace App\Livewire;

use App\Models\Avaliacao;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class MinhasAvaliacoes extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $pacienteFilter = '';
    public $dataInicio = '';
    public $dataFim = '';

    public function mount()
    {
        // Apenas profissionais podem acessar
        if (!Auth::user()->hasRole('Profissional') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Apenas profissionais podem visualizar suas avaliações.');
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

    public function updatingPacienteFilter()
    {
        $this->resetPage();
    }

    public function limparFiltros()
    {
        $this->reset(['search', 'statusFilter', 'pacienteFilter', 'dataInicio', 'dataFim']);
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = Avaliacao::query()
            ->with(['paciente', 'template'])
            ->where('user_id', $user->id);

        // Filtro de busca (nome do paciente ou template)
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
        $total = Avaliacao::where('user_id', $user->id)->count();
        $rascunho = Avaliacao::where('user_id', $user->id)->where('status', 'Rascunho')->count();
        $finalizadas = Avaliacao::where('user_id', $user->id)->where('status', 'Finalizado')->count();

        // Lista de pacientes para filtro
        $pacientes = \App\Models\Paciente::whereHas('avaliacoes', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->orderBy('nome_completo')->get();

        return view('livewire.minhas-avaliacoes', [
            'avaliacoes' => $avaliacoes,
            'total' => $total,
            'rascunho' => $rascunho,
            'finalizadas' => $finalizadas,
            'pacientes' => $pacientes,
        ]);
    }
}

