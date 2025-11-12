<?php

namespace App\Livewire;

use App\Models\Evolucao;
use App\Models\Paciente;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class FormEvolucao extends Component
{
    public ?Evolucao $evolucao = null;
    public ?int $evolucaoId = null;
    public ?int $pacienteId = null;
    public ?int $atendimentoId = null;
    public ?int $evolucaoPaiId = null; // Para criar adendo

    // Campos do formulário
    public string $relato_clinico = '';
    public string $conduta = '';
    public string $objetivos = '';
    public string $status = 'Rascunho';

    // Estado do componente
    public bool $isEditMode = false;
    public bool $isFinalizado = false;

    public bool $isAdendo = false;
    public string $tituloModal = 'Preencher Evolução';

    public function mount(?int $evolucaoId = null, ?int $pacienteId = null, ?int $atendimentoId = null, ?int $evolucaoPaiId = null, ?int $evolucao_id = null)
    {
        // Verifica permissão - Secretaria não tem acesso
        if (Auth::user()->hasRole('Secretaria')) {
            abort(403, 'Você não tem acesso a este módulo.');
        }
        
        // Verifica permissão básica
        if (!Auth::user()->can('criar_evolucao') && !Auth::user()->can('editar_evolucao') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para criar ou editar evoluções.');
        }
        
        // Suporta tanto evolucaoId quanto evolucao_id (para compatibilidade)
        $this->evolucaoId = $evolucaoId ?? $evolucao_id;
        
        // Aceita parâmetros via query string também
        $request = request();
        $this->evolucaoPaiId = $evolucaoPaiId ?? $request->query('evolucao_pai_id');
        $this->pacienteId = $pacienteId ?? $request->query('paciente_id');
        $this->atendimentoId = $atendimentoId;

        if ($this->evolucaoPaiId) {
            // Cenário 1: Criando um Adendo
            $this->isAdendo = true;
            $evolucaoPai = Evolucao::findOrFail($this->evolucaoPaiId);
            
            // Regra de Segurança: Só pode criar adendo se a evolução pai estiver finalizada
            if ($evolucaoPai->status !== 'Finalizado') {
                session()->flash('error', 'Apenas evoluções finalizadas podem ter adendos.');
                return redirect()->route('app.evolucoes');
            }
            
            $this->evolucao = new Evolucao(); // Nova evolução
            $this->tituloModal = 'Adicionar Adendo';
            $this->pacienteId = $evolucaoPai->paciente_id;
            $this->atendimentoId = $evolucaoPai->atendimento_id;
        } elseif ($this->evolucaoId) {
            // Cenário 2: Editando um Rascunho
            $this->evolucao = Evolucao::findOrFail($this->evolucaoId);
            
            // Regra de Segurança: Só pode editar se for dele e for rascunho (Admin pode editar qualquer uma)
            if (!Auth::user()->hasRole('Admin')) {
                if ($this->evolucao->user_id != Auth::id() || $this->evolucao->status != 'Rascunho') {
                    session()->flash('error', 'Acesso negado. Você só pode editar suas próprias evoluções em rascunho.');
                    return redirect()->route('app.evolucoes');
                }
            } elseif ($this->evolucao->status != 'Rascunho') {
                // Admin pode editar apenas rascunhos
                session()->flash('error', 'Apenas evoluções em rascunho podem ser editadas.');
                return redirect()->route('app.evolucoes');
            }

            $this->isEditMode = true;
            $this->isFinalizado = $this->evolucao->status === 'Finalizado';
            
            // Carrega os dados
            $this->relato_clinico = $this->evolucao->relato_clinico ?? '';
            $this->conduta = $this->evolucao->conduta ?? '';
            $this->objetivos = $this->evolucao->objetivos ?? '';
            $this->status = $this->evolucao->status;
            $this->pacienteId = $this->evolucao->paciente_id;
            $this->atendimentoId = $this->evolucao->atendimento_id;
        } else {
            // Cenário 3: Erro (não deveria ser chamado sem ID)
            session()->flash('error', 'Erro ao carregar evolução.');
            return redirect()->route('app.evolucoes');
        }
    }

    public function salvarRascunho()
    {
        $this->validate([
            'relato_clinico' => 'nullable|string',
            'conduta' => 'nullable|string',
            'objetivos' => 'nullable|string',
        ]);

        if ($this->isEditMode && $this->evolucao) {
            // Atualiza evolução existente
            $this->evolucao->update([
                'relato_clinico' => $this->relato_clinico,
                'conduta' => $this->conduta,
                'objetivos' => $this->objetivos,
                'status' => 'Rascunho',
            ]);
        } else {
            // Cria nova evolução
            $this->evolucao = Evolucao::create([
                'paciente_id' => $this->pacienteId,
                'user_id' => Auth::id(),
                'atendimento_id' => $this->atendimentoId,
                'evolucao_pai_id' => $this->evolucaoPaiId,
                'relato_clinico' => $this->relato_clinico,
                'conduta' => $this->conduta,
                'objetivos' => $this->objetivos,
                'status' => 'Rascunho',
            ]);
            $this->isEditMode = true;
            $this->evolucaoId = $this->evolucao->id;
        }

        session()->flash('message', 'Evolução salva como rascunho com sucesso!');
        // Não redireciona, mantém o formulário aberto (Fluxo 2.3)
    }

    public function finalizar()
    {
        $this->validate([
            'relato_clinico' => 'required|string',
        ], [
            'relato_clinico.required' => 'O relato clínico é obrigatório para finalizar.',
        ]);

        if ($this->isEditMode && $this->evolucao && $this->evolucao->id) {
            $this->evolucao->update([
                'relato_clinico' => $this->relato_clinico,
                'conduta' => $this->conduta,
                'objetivos' => $this->objetivos,
                'status' => 'Finalizado',
                'finalizado_em' => now(),
            ]);
        } else {
            $this->evolucao = Evolucao::create([
                'paciente_id' => $this->pacienteId,
                'user_id' => Auth::id(),
                'atendimento_id' => $this->atendimentoId,
                'evolucao_pai_id' => $this->evolucaoPaiId,
                'relato_clinico' => $this->relato_clinico,
                'conduta' => $this->conduta,
                'objetivos' => $this->objetivos,
                'status' => 'Finalizado',
                'finalizado_em' => now(),
            ]);
            $this->isEditMode = true;
            $this->evolucaoId = $this->evolucao->id;
        }

        $this->isFinalizado = true;
        session()->flash('message', 'Evolução finalizada com sucesso!');
        $this->dispatch('evolucao-salva'); // Dispara evento para o Dashboard
        
        // Redireciona para o prontuário (Fluxo 2.3)
        return redirect()->route('app.pacientes.prontuario', ['pacienteId' => $this->pacienteId]);
    }

    /**
     * Salvar adendo (adendos já nascem finalizados)
     */
    public function salvarAdendo()
    {
        $this->validate([
            'relato_clinico' => 'required|string',
        ], [
            'relato_clinico.required' => 'O relato clínico é obrigatório.',
        ]);

        $evolucaoPai = Evolucao::findOrFail($this->evolucaoPaiId);

        // Cria o *novo* registro de adendo
        Evolucao::create([
            'paciente_id' => $evolucaoPai->paciente_id,
            'user_id' => Auth::id(),
            'atendimento_id' => $evolucaoPai->atendimento_id,
            'evolucao_pai_id' => $this->evolucaoPaiId,
            'status' => 'Finalizado', // Adendos já nascem finalizados
            'finalizado_em' => now(),
            'relato_clinico' => $this->relato_clinico,
            'conduta' => $this->conduta,
            'objetivos' => $this->objetivos,
        ]);

        $this->dispatch('adendo-salvo'); // Dispara evento para o Prontuário
        session()->flash('message', 'Adendo salvo com sucesso!');
        
        // Redireciona para o prontuário
        return redirect()->route('app.pacientes.prontuario', ['pacienteId' => $evolucaoPai->paciente_id]);
    }


    public function render()
    {
        $paciente = $this->pacienteId ? Paciente::find($this->pacienteId) : null;
        
        return view('livewire.form-evolucao', [
            'paciente' => $paciente,
        ]);
    }
}
