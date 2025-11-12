<?php

namespace App\Livewire;

use App\Models\AvaliacaoTemplate;
use App\Models\AvaliacaoPergunta;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class GerenciarTemplatesAvaliacao extends Component
{
    use WithPagination;

    public $mostrarModalTemplate = false;
    public $mostrarModalPergunta = false;
    public $templateId = null;
    public $perguntaId = null;
    public $templateSelecionado = null;
    
    // Campos do template
    public $nome_template = '';
    public $status = true;
    
    // Campos da pergunta
    public $titulo_pergunta = '';
    public $tipo_campo = 'texto_curto';
    public $ordem = 0;

    public function mount()
    {
        // Verifica permissão - Coordenador e Admin
        if (!Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para gerenciar templates de avaliação.');
        }
    }

    public function abrirModalTemplate($templateId = null)
    {
        $this->templateId = $templateId;
        $this->mostrarModalTemplate = true;

        if ($templateId) {
            $template = AvaliacaoTemplate::with('perguntas')->findOrFail($templateId);
            $this->nome_template = $template->nome_template;
            $this->status = $template->status;
            $this->templateSelecionado = $template;
        } else {
            $this->reset(['nome_template', 'status']);
            $this->status = true;
            $this->templateSelecionado = null;
        }
    }

    public function fecharModalTemplate()
    {
        $this->mostrarModalTemplate = false;
        $this->reset(['templateId', 'nome_template', 'status', 'templateSelecionado']);
        $this->status = true;
    }

    public function salvarTemplate()
    {
        $this->validate([
            'nome_template' => 'required|string|max:255',
            'status' => 'boolean',
        ], [
            'nome_template.required' => 'O nome do template é obrigatório.',
        ]);

        if ($this->templateId) {
            $template = AvaliacaoTemplate::findOrFail($this->templateId);
            $template->update([
                'nome_template' => $this->nome_template,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Template atualizado com sucesso!');
        } else {
            $template = AvaliacaoTemplate::create([
                'nome_template' => $this->nome_template,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Template criado com sucesso!');
        }

        $this->fecharModalTemplate();
    }

    public function abrirModalPergunta($templateId, $perguntaId = null)
    {
        $this->templateSelecionado = AvaliacaoTemplate::with('perguntas')->findOrFail($templateId);
        $this->perguntaId = $perguntaId;
        $this->mostrarModalPergunta = true;

        if ($perguntaId) {
            $pergunta = AvaliacaoPergunta::where('id', $perguntaId)
                ->where('avaliacao_template_id', $templateId)
                ->firstOrFail();
            
            $this->titulo_pergunta = $pergunta->titulo_pergunta;
            $this->tipo_campo = $pergunta->tipo_campo;
            $this->ordem = $pergunta->ordem;
        } else {
            // Define ordem como próxima disponível
            $ultimaOrdem = $this->templateSelecionado->perguntas->max('ordem') ?? 0;
            $this->ordem = $ultimaOrdem + 1;
            $this->reset(['titulo_pergunta', 'tipo_campo']);
            $this->tipo_campo = 'texto_curto';
        }
    }

    public function fecharModalPergunta()
    {
        $this->mostrarModalPergunta = false;
        $this->reset(['perguntaId', 'titulo_pergunta', 'tipo_campo', 'ordem']);
        $this->tipo_campo = 'texto_curto';
    }

    public function salvarPergunta()
    {
        if (!$this->templateSelecionado) {
            return;
        }

        $this->validate([
            'titulo_pergunta' => 'required|string|max:255',
            'tipo_campo' => 'required|in:texto_curto,texto_longo,data,sim_nao',
            'ordem' => 'required|integer|min:0',
        ], [
            'titulo_pergunta.required' => 'O título da pergunta é obrigatório.',
            'tipo_campo.required' => 'O tipo de campo é obrigatório.',
            'ordem.required' => 'A ordem é obrigatória.',
        ]);

        if ($this->perguntaId) {
            $pergunta = AvaliacaoPergunta::where('id', $this->perguntaId)
                ->where('avaliacao_template_id', $this->templateSelecionado->id)
                ->firstOrFail();
            
            $pergunta->update([
                'titulo_pergunta' => $this->titulo_pergunta,
                'tipo_campo' => $this->tipo_campo,
                'ordem' => $this->ordem,
            ]);

            session()->flash('message', 'Pergunta atualizada com sucesso!');
        } else {
            AvaliacaoPergunta::create([
                'avaliacao_template_id' => $this->templateSelecionado->id,
                'titulo_pergunta' => $this->titulo_pergunta,
                'tipo_campo' => $this->tipo_campo,
                'ordem' => $this->ordem,
            ]);

            session()->flash('message', 'Pergunta adicionada com sucesso!');
        }

        $this->templateSelecionado->refresh();
        $this->fecharModalPergunta();
    }

    public function deletarTemplate($templateId)
    {
        $template = AvaliacaoTemplate::findOrFail($templateId);
        
        // Verifica se há avaliações usando este template
        if ($template->avaliacoes()->count() > 0) {
            session()->flash('error', 'Não é possível deletar este template. Existem avaliações usando ele.');
            return;
        }

        $template->delete();
        session()->flash('message', 'Template removido com sucesso!');
    }

    public function deletarPergunta($templateId, $perguntaId)
    {
        $pergunta = AvaliacaoPergunta::where('id', $perguntaId)
            ->where('avaliacao_template_id', $templateId)
            ->firstOrFail();

        $pergunta->delete();
        
        if ($this->templateSelecionado && $this->templateSelecionado->id == $templateId) {
            $this->templateSelecionado->refresh();
        }
        
        session()->flash('message', 'Pergunta removida com sucesso!');
    }

    public function render()
    {
        $templates = AvaliacaoTemplate::withCount('perguntas')
            ->orderBy('nome_template')
            ->paginate(15);

        return view('livewire.gerenciar-templates-avaliacao', [
            'templates' => $templates,
        ]);
    }
}

