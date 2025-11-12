<?php

namespace App\Livewire;

use App\Models\Avaliacao;
use App\Models\AvaliacaoTemplate;
use App\Models\AvaliacaoPergunta;
use App\Models\AvaliacaoResposta;
use App\Models\Paciente;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class AplicarAvaliacao extends Component
{
    // Passo 1: Seleção
    public ?int $pacienteId = null;
    public ?int $templateId = null;
    public int $passoAtual = 1;

    // Passo 2: Respostas
    public array $respostas = [];
    public string $status = 'Rascunho';

    // Estado
    public ?Avaliacao $avaliacao = null;
    public ?AvaliacaoTemplate $template = null;

    public function mount(?int $avaliacaoId = null, ?int $pacienteId = null)
    {
        // Verifica permissão - Admin tem acesso total
        if (!Auth::user()->can('aplicar_avaliacao') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para aplicar avaliações.');
        }
        
        if ($avaliacaoId) {
            // Modo edição de rascunho
            $this->avaliacao = Avaliacao::with(['template.perguntas', 'respostas'])->findOrFail($avaliacaoId);
            $this->pacienteId = $this->avaliacao->paciente_id;
            $this->templateId = $this->avaliacao->avaliacao_template_id;
            $this->status = $this->avaliacao->status;
            $this->template = $this->avaliacao->template;
            $this->passoAtual = 2;

            // Carrega respostas existentes
            foreach ($this->avaliacao->respostas as $resposta) {
                $this->respostas[$resposta->avaliacao_pergunta_id] = $resposta->resposta;
            }
        } elseif ($pacienteId) {
            // Pré-seleciona paciente se fornecido
            $this->pacienteId = $pacienteId;
        }
    }

    /**
     * Quando o template é selecionado, carrega as perguntas (Fluxo 3.2)
     */
    public function updatedTemplateId($value)
    {
        if ($value) {
            $this->template = AvaliacaoTemplate::with('perguntas')->findOrFail($value);
            // Inicializa respostas vazias
            foreach ($this->template->perguntas as $pergunta) {
                if (!isset($this->respostas[$pergunta->id])) {
                    $this->respostas[$pergunta->id] = '';
                }
            }
        } else {
            $this->template = null;
            $this->respostas = [];
        }
    }

    public function avancarPasso1()
    {
        $this->validate([
            'pacienteId' => 'required|exists:pacientes,id',
            'templateId' => 'required|exists:avaliacao_templates,id',
        ], [
            'pacienteId.required' => 'Selecione um paciente.',
            'templateId.required' => 'Selecione um template de avaliação.',
        ]);

        if (!$this->template) {
            $this->template = AvaliacaoTemplate::with('perguntas')->findOrFail($this->templateId);
        }

        $this->passoAtual = 2;

        // Cria a avaliação em rascunho (Fluxo 3.2)
        if (!$this->avaliacao) {
            $this->avaliacao = Avaliacao::create([
                'paciente_id' => $this->pacienteId,
                'user_id' => Auth::id(),
                'avaliacao_template_id' => $this->templateId,
                'status' => 'Rascunho',
            ]);
        }

        // Inicializa respostas vazias
        foreach ($this->template->perguntas as $pergunta) {
            if (!isset($this->respostas[$pergunta->id])) {
                $this->respostas[$pergunta->id] = '';
            }
        }
    }

    /**
     * Atualiza resposta automaticamente (Fluxo 3.2 - Rascunho Automático)
     * Livewire v3 chama este método quando uma propriedade aninhada é atualizada
     * O formato da chave será "respostas.{perguntaId}" ou apenas o índice numérico
     */
    public function updatedRespostas($value, $key)
    {
        // Extrai o ID da pergunta da chave
        // Pode ser "respostas.123" ou apenas "123" dependendo da versão do Livewire
        $perguntaId = is_numeric($key) ? (int) $key : (int) str_replace('respostas.', '', $key);
        
        // Se ainda não tem avaliação criada, não salva (será salvo quando avançar para passo 2)
        if ($this->avaliacao && $perguntaId > 0 && $value !== null && $value !== '') {
            AvaliacaoResposta::updateOrCreate(
                [
                    'avaliacao_id' => $this->avaliacao->id,
                    'avaliacao_pergunta_id' => $perguntaId,
                ],
                [
                    'resposta' => $value,
                ]
            );
        }
    }

    public function voltarPasso1()
    {
        $this->passoAtual = 1;
    }

    public function salvarRascunho()
    {
        // Não valida obrigatoriedade no rascunho (Fluxo 3.2)
        if (!$this->avaliacao) {
            $this->avaliacao = Avaliacao::create([
                'paciente_id' => $this->pacienteId,
                'user_id' => Auth::id(),
                'avaliacao_template_id' => $this->templateId,
                'status' => 'Rascunho',
            ]);
        } else {
            $this->avaliacao->update(['status' => 'Rascunho']);
        }

        // Salva/atualiza respostas (apenas as que foram preenchidas)
        foreach ($this->respostas as $perguntaId => $resposta) {
            if ($resposta !== null && $resposta !== '') {
                AvaliacaoResposta::updateOrCreate(
                    [
                        'avaliacao_id' => $this->avaliacao->id,
                        'avaliacao_pergunta_id' => $perguntaId,
                    ],
                    [
                        'resposta' => $resposta,
                    ]
                );
            }
        }

        session()->flash('message', 'Avaliação salva como rascunho com sucesso!');
    }

    public function finalizarAvaliacao()
    {
        $this->validarRespostas();

        if ($this->avaliacao) {
            $this->avaliacao->update(['status' => 'Finalizado']);
        } else {
            $this->avaliacao = Avaliacao::create([
                'paciente_id' => $this->pacienteId,
                'user_id' => Auth::id(),
                'avaliacao_template_id' => $this->templateId,
                'status' => 'Finalizado',
            ]);
        }

        // Salva respostas
        foreach ($this->respostas as $perguntaId => $resposta) {
            AvaliacaoResposta::updateOrCreate(
                [
                    'avaliacao_id' => $this->avaliacao->id,
                    'avaliacao_pergunta_id' => $perguntaId,
                ],
                [
                    'resposta' => $resposta,
                ]
            );
        }

        $this->status = 'Finalizado';
        session()->flash('message', 'Avaliação finalizada com sucesso!');
        $this->dispatch('avaliacao-finalizada');
        
        // Redireciona para o prontuário (Fluxo 3.3)
        return redirect()->route('app.pacientes.prontuario', ['pacienteId' => $this->pacienteId]);
    }

    private function validarRespostas()
    {
        $rules = [];
        foreach ($this->template->perguntas as $pergunta) {
            $rules["respostas.{$pergunta->id}"] = 'required';
        }

        $this->validate($rules, [
            'respostas.*.required' => 'Esta pergunta é obrigatória.',
        ]);
    }

    public function render()
    {
        // Filtra pacientes por unidade do usuário
        $queryPacientes = Paciente::where('status', 'Ativo');
        
        if (!Auth::user()->hasRole('Admin')) {
            $unidadeIds = Auth::user()->unidades->pluck('id')->toArray();
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada && in_array($unidadeSelecionada, $unidadeIds)) {
                $queryPacientes->where('unidade_padrao_id', $unidadeSelecionada);
            } else {
                $queryPacientes->whereIn('unidade_padrao_id', $unidadeIds);
            }
        } else {
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $queryPacientes->where('unidade_padrao_id', $unidadeSelecionada);
            }
        }
        
        $pacientes = $queryPacientes->orderBy('nome_completo')->get();
        $templates = AvaliacaoTemplate::where('status', true)->orderBy('nome_template')->get();

        return view('livewire.aplicar-avaliacao', [
            'pacientes' => $pacientes,
            'templates' => $templates,
        ]);
    }
}
