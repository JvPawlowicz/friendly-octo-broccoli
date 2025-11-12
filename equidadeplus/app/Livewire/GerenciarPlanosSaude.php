<?php

namespace App\Livewire;

use App\Models\PlanoSaude;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class GerenciarPlanosSaude extends Component
{
    use WithPagination;

    public $mostrarModal = false;
    public $planoId = null;
    public $nome_plano = '';
    public $codigo_ans = '';
    public $status = true;

    public function mount()
    {
        // Verifica permissão - Secretaria, Coordenador e Admin
        if (!Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria'])) {
            abort(403, 'Você não tem permissão para gerenciar planos de saúde.');
        }
    }

    public function abrirModal($planoId = null)
    {
        $this->planoId = $planoId;
        $this->mostrarModal = true;

        if ($planoId) {
            $plano = PlanoSaude::findOrFail($planoId);
            $this->nome_plano = $plano->nome_plano;
            $this->codigo_ans = $plano->codigo_ans;
            $this->status = $plano->status;
        } else {
            $this->reset(['nome_plano', 'codigo_ans', 'status']);
            $this->status = true;
        }
    }

    public function fecharModal()
    {
        $this->mostrarModal = false;
        $this->reset(['planoId', 'nome_plano', 'codigo_ans', 'status']);
        $this->status = true;
    }

    public function salvar()
    {
        $this->validate([
            'nome_plano' => 'required|string|max:255',
            'codigo_ans' => 'nullable|string|max:20',
            'status' => 'boolean',
        ], [
            'nome_plano.required' => 'O nome do plano é obrigatório.',
        ]);

        if ($this->planoId) {
            $plano = PlanoSaude::findOrFail($this->planoId);
            $plano->update([
                'nome_plano' => $this->nome_plano,
                'codigo_ans' => $this->codigo_ans ?: null,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Plano de saúde atualizado com sucesso!');
        } else {
            PlanoSaude::create([
                'nome_plano' => $this->nome_plano,
                'codigo_ans' => $this->codigo_ans ?: null,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Plano de saúde cadastrado com sucesso!');
        }

        $this->fecharModal();
    }

    public function deletar($planoId)
    {
        $plano = PlanoSaude::findOrFail($planoId);
        
        // Verifica se há pacientes vinculados
        if ($plano->pacientes()->count() > 0) {
            session()->flash('error', 'Não é possível deletar este plano. Existem pacientes vinculados a ele.');
            return;
        }

        $plano->delete();
        session()->flash('message', 'Plano de saúde removido com sucesso!');
    }

    public function render()
    {
        $planos = PlanoSaude::orderBy('nome_plano')->paginate(15);
        
        return view('livewire.gerenciar-planos-saude', [
            'planos' => $planos,
        ]);
    }
}

