<?php

namespace App\Livewire;

use App\Models\Paciente;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class ListaPacientes extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $unidadeFilter = '';

    public function mount()
    {
        // Verifica permissão - Admin tem acesso total
        if (!Auth::user()->can('ver_pacientes') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para visualizar pacientes.');
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

    public function updatingUnidadeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Paciente::query();

        // Filtro por unidade do usuário
        if (!Auth::user()->hasRole('Admin')) {
            $unidadeIds = Auth::user()->unidades->pluck('id')->toArray();
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada && in_array($unidadeSelecionada, $unidadeIds)) {
                $query->where('unidade_padrao_id', $unidadeSelecionada);
            } else {
                $query->whereIn('unidade_padrao_id', $unidadeIds);
            }
        } else {
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $query->where('unidade_padrao_id', $unidadeSelecionada);
            }
        }

        // Filtro de busca
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nome_completo', 'like', '%' . $this->search . '%')
                  ->orWhere('cpf', 'like', '%' . $this->search . '%')
                  ->orWhere('email_principal', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro de status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filtro de unidade (se Admin)
        if ($this->unidadeFilter && Auth::user()->hasRole('Admin')) {
            $query->where('unidade_padrao_id', $this->unidadeFilter);
        }

        $pacientes = $query->with(['unidadePadrao', 'planoSaude'])
            ->orderBy('nome_completo')
            ->paginate(15);

        $unidades = Auth::user()->hasRole('Admin') 
            ? \App\Models\Unidade::orderBy('nome')->get()
            : Auth::user()->unidades;

        return view('livewire.lista-pacientes', [
            'pacientes' => $pacientes,
            'unidades' => $unidades,
        ]);
    }
}

