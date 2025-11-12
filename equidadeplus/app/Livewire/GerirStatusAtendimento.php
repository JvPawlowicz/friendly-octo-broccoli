<?php

namespace App\Livewire;

use App\Models\Atendimento;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GerirStatusAtendimento extends Component
{
    public int $atendimentoId;
    public string $statusAtual;
    public bool $mostrarModal = false;

    public function mount(int $atendimentoId)
    {
        $this->atendimentoId = $atendimentoId;
        $atendimento = Atendimento::findOrFail($atendimentoId);
        $this->statusAtual = $atendimento->status;
    }

    public function abrirModal()
    {
        $this->mostrarModal = true;
    }

    public function fecharModal()
    {
        $this->mostrarModal = false;
    }

    /**
     * Atualiza o status do atendimento (Fluxo 2.2)
     */
    public function mudarStatus(string $novoStatus)
    {
        $atendimento = Atendimento::findOrFail($this->atendimentoId);
        
        // Verifica permissão (apenas o profissional do atendimento ou admin)
        if ($atendimento->user_id !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            session()->flash('error', 'Você não tem permissão para alterar este atendimento.');
            return;
        }

        $atendimento->update(['status' => $novoStatus]);
        $this->statusAtual = $novoStatus;

        // Dispara eventos para broadcast
        event(new \App\Events\AtendimentoAtualizado($atendimento));
        
        if ($novoStatus === 'Concluído') {
            event(new \App\Events\AtendimentoConcluido($atendimento));
        }

        $this->fecharModal();
        session()->flash('message', 'Status atualizado com sucesso!');
        $this->dispatch('status-atualizado');
        $this->dispatch('fechar-modal-status');
    }

    public function render()
    {
        $atendimento = Atendimento::with(['paciente', 'profissional'])->findOrFail($this->atendimentoId);
        
        return view('livewire.gerir-status-atendimento', [
            'atendimento' => $atendimento,
        ]);
    }
}
