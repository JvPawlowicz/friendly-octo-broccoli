<?php

namespace App\Livewire;

use App\Models\Paciente;
use App\Models\Responsavel;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class GerenciarResponsaveis extends Component
{
    public int $pacienteId;
    public Paciente $paciente;
    public $mostrarModal = false;
    public $responsavelId = null;
    
    // Campos do formulário
    public $nome_completo = '';
    public $parentesco = '';
    public $email = '';
    public $telefone_principal = '';
    public $cpf = '';
    public $is_responsavel_legal = false;
    public $is_contato_emergencia = false;
    public $recebe_comunicacoes = true;

    public function mount(int $pacienteId)
    {
        // Verifica permissão
        if (!Auth::user()->can('editar_paciente') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria'])) {
            abort(403, 'Você não tem permissão para gerenciar responsáveis.');
        }

        $this->pacienteId = $pacienteId;
        $this->paciente = Paciente::with('responsaveis')->findOrFail($pacienteId);
    }

    public function abrirModal($responsavelId = null)
    {
        $this->responsavelId = $responsavelId;
        $this->mostrarModal = true;

        if ($responsavelId) {
            $responsavel = Responsavel::where('id', $responsavelId)
                ->where('paciente_id', $this->pacienteId)
                ->firstOrFail();
            
            $this->nome_completo = $responsavel->nome_completo;
            $this->parentesco = $responsavel->parentesco;
            $this->email = $responsavel->email;
            $this->telefone_principal = $responsavel->telefone_principal;
            $this->cpf = $responsavel->cpf;
            $this->is_responsavel_legal = $responsavel->is_responsavel_legal;
            $this->is_contato_emergencia = $responsavel->is_contato_emergencia;
            $this->recebe_comunicacoes = $responsavel->recebe_comunicacoes;
        } else {
            $this->reset(['nome_completo', 'parentesco', 'email', 'telefone_principal', 'cpf', 'is_responsavel_legal', 'is_contato_emergencia', 'recebe_comunicacoes']);
            $this->recebe_comunicacoes = true;
        }
    }

    public function fecharModal()
    {
        $this->mostrarModal = false;
        $this->reset(['responsavelId', 'nome_completo', 'parentesco', 'email', 'telefone_principal', 'cpf', 'is_responsavel_legal', 'is_contato_emergencia', 'recebe_comunicacoes']);
        $this->recebe_comunicacoes = true;
    }

    public function salvar()
    {
        $this->validate([
            'nome_completo' => 'required|string|max:255',
            'parentesco' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone_principal' => 'required|string|max:20',
            'cpf' => 'nullable|string|max:14',
        ], [
            'nome_completo.required' => 'O nome é obrigatório.',
            'parentesco.required' => 'O parentesco é obrigatório.',
            'telefone_principal.required' => 'O telefone é obrigatório.',
            'email.email' => 'O email deve ser válido.',
        ]);

        if ($this->responsavelId) {
            $responsavel = Responsavel::where('id', $this->responsavelId)
                ->where('paciente_id', $this->pacienteId)
                ->firstOrFail();
            
            $responsavel->update([
                'nome_completo' => $this->nome_completo,
                'parentesco' => $this->parentesco,
                'email' => $this->email ?: null,
                'telefone_principal' => $this->telefone_principal,
                'cpf' => $this->cpf ?: null,
                'is_responsavel_legal' => $this->is_responsavel_legal,
                'is_contato_emergencia' => $this->is_contato_emergencia,
                'recebe_comunicacoes' => $this->recebe_comunicacoes,
            ]);

            session()->flash('message', 'Responsável atualizado com sucesso!');
        } else {
            Responsavel::create([
                'paciente_id' => $this->pacienteId,
                'nome_completo' => $this->nome_completo,
                'parentesco' => $this->parentesco,
                'email' => $this->email ?: null,
                'telefone_principal' => $this->telefone_principal,
                'cpf' => $this->cpf ?: null,
                'is_responsavel_legal' => $this->is_responsavel_legal,
                'is_contato_emergencia' => $this->is_contato_emergencia,
                'recebe_comunicacoes' => $this->recebe_comunicacoes,
            ]);

            session()->flash('message', 'Responsável adicionado com sucesso!');
        }

        $this->paciente->refresh();
        $this->fecharModal();
    }

    public function deletar($responsavelId)
    {
        $responsavel = Responsavel::where('id', $responsavelId)
            ->where('paciente_id', $this->pacienteId)
            ->firstOrFail();

        $responsavel->delete();

        $this->paciente->refresh();
        session()->flash('message', 'Responsável removido com sucesso!');
    }

    public function render()
    {
        return view('livewire.gerenciar-responsaveis');
    }
}

