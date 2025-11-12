<?php

namespace App\Livewire;

use App\Models\Paciente;
use App\Models\Documento;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.app')]
class ProntuarioView extends Component
{
    use WithFileUploads;
    public int $pacienteId;
    public Paciente $paciente;
    public $linhaTempo = [];
    public $mostrarModalUpload = false;
    public $documentoParaDeletar = null;
    public $mostrarModalConfirmacao = false;
    public array $destaques = [];
    public array $documentosRecentes = [];

    // Campos do formulário de upload
    public $titulo_documento = '';
    public $categoria = '';
    public $arquivo = null;

    /**
     * "Ouve" o evento disparado pelo FormEvolucao (Adendo)
     */
    #[On('adendo-salvo')]
    public function atualizarTimeline()
    {
        $this->carregarLinhaTempo();
    }

    public function mount(int $pacienteId)
    {
        // Verifica permissão - Admin tem acesso total
        if (!Auth::user()->can('ver_prontuario') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para visualizar prontuários.');
        }
        
        $this->pacienteId = $pacienteId;
        $this->paciente = Paciente::with([
            'evolucoes.profissional',
            'evolucoes.adendos.profissional',
            'avaliacoes.profissional',
            'avaliacoes.template',
            'avaliacoes.respostas.pergunta',
            'documentos.user',
            'planoSaude',
            'unidadePadrao',
            'responsaveis',
            'atendimentos.profissional',
            'atendimentos.sala',
        ])->findOrFail($pacienteId);

        // Verifica se o usuário tem acesso à unidade do paciente
        $user = Auth::user();
        if (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            if (!in_array($this->paciente->unidade_padrao_id, $unidadeIds)) {
                abort(403, 'Você não tem acesso a este prontuário. O paciente pertence a uma unidade diferente.');
            }
        }

        $this->carregarLinhaTempo();
        $this->carregarDestaques();
    }

    /**
     * Carrega e mistura (ordenado por data) dados das relações:
     * - paciente->evolucoes() (apenas evoluções-pai, sem adendos)
     * - paciente->avaliacoes() (apenas finalizadas)
     * - paciente->documentos()
     * 
     * Regra: Esta será a "Linha do Tempo" (Item 4.2 - Fluxo 4)
     */
    public function carregarLinhaTempo()
    {
        $itens = [];

        // Adiciona Evoluções (apenas as "pai", finalizadas)
        $evolucoes = $this->paciente->evolucoes()
            ->whereNull('evolucao_pai_id')
            ->where('status', 'Finalizado')
            ->with(['profissional', 'adendos.profissional'])
            ->get();

        foreach ($evolucoes as $evolucao) {
            $itens[] = [
                'tipo' => 'evolucao',
                'id' => $evolucao->id,
                'data' => $evolucao->finalizado_em ?? $evolucao->created_at,
                'titulo' => 'Evolução Clínica',
                'subtitulo' => 'Profissional: ' . $evolucao->profissional->name,
                'status' => $evolucao->status,
                'dados' => $evolucao,
            ];
        }

        // Adiciona Avaliações (apenas finalizadas)
        $avaliacoes = $this->paciente->avaliacoes()
            ->where('status', 'Finalizado')
            ->with(['profissional', 'template', 'respostas.pergunta'])
            ->get();

        foreach ($avaliacoes as $avaliacao) {
            $itens[] = [
                'tipo' => 'avaliacao',
                'id' => $avaliacao->id,
                'data' => $avaliacao->created_at,
                'titulo' => 'Avaliação: ' . $avaliacao->template->nome_template,
                'subtitulo' => 'Profissional: ' . $avaliacao->profissional->name,
                'status' => $avaliacao->status,
                'dados' => $avaliacao,
            ];
        }

        // Adiciona Documentos
        foreach ($this->paciente->documentos as $documento) {
            $itens[] = [
                'tipo' => 'documento',
                'id' => $documento->id,
                'data' => $documento->created_at,
                'titulo' => $documento->titulo_documento,
                'subtitulo' => 'Categoria: ' . ($documento->categoria ?? 'Sem categoria') . ' | Upload por: ' . ($documento->user->name ?? 'N/A'),
                'status' => null,
                'dados' => $documento,
            ];
        }

        // Ordena por data (mais recente primeiro)
        usort($itens, function ($a, $b) {
            return $b['data'] <=> $a['data'];
        });

        $this->linhaTempo = $itens;
    }

    protected function carregarDestaques(): void
    {
        $paciente = $this->paciente;

        $responsaveis = $paciente->responsaveis->map(function ($responsavel) {
            return [
                'nome' => $responsavel->nome_completo ?? 'Responsável',
                'parentesco' => $responsavel->parentesco ?? null,
                'telefone' => $responsavel->telefone_principal ?? null,
            ];
        })->take(2)->values()->all();

        $proximoAtendimento = $paciente->atendimentos
            ->filter(function ($atendimento) {
                return in_array($atendimento->status, ['Agendado', 'Confirmado', 'Check-in'])
                    && $atendimento->data_hora_inicio?->isFuture();
            })
            ->sortBy(fn ($atendimento) => $atendimento->data_hora_inicio)
            ->first();

        $ultimaEvolucao = $paciente->evolucoes
            ->filter(fn ($e) => $e->status === 'Finalizado')
            ->sortByDesc(fn ($e) => $e->finalizado_em ?? $e->updated_at ?? $e->created_at)
            ->first();

        $pendenciasEvolucao = $paciente->evolucoes->filter(fn ($e) => $e->status === 'Rascunho')->count();
        $pendenciasAvaliacao = $paciente->avaliacoes->filter(fn ($a) => $a->status !== 'Finalizado')->count();

        $this->documentosRecentes = $paciente->documentos
            ->sortByDesc('created_at')
            ->take(3)
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'titulo' => $doc->titulo_documento,
                    'created_at' => $doc->created_at,
                    'categoria' => $doc->categoria,
                    'usuario' => $doc->user?->name,
                ];
            })->values()->all();

        $this->destaques = [
            'idade' => optional($paciente->data_nascimento)->age,
            'plano' => $paciente->planoSaude?->nome_plano ?? 'Particular',
            'responsaveis' => $responsaveis,
            'diagnostico' => $paciente->diagnostico_condicao,
            'alergias' => $paciente->alergias_medicacoes,
            'plano_crise' => $paciente->plano_de_crise,
            'proximo_atendimento' => $proximoAtendimento ? [
                'horario' => $proximoAtendimento->data_hora_inicio?->format('d/m/Y H:i'),
                'profissional' => $proximoAtendimento->profissional?->name,
                'status' => $proximoAtendimento->status,
                'sala' => $proximoAtendimento->sala?->nome,
            ] : null,
            'ultima_evolucao' => $ultimaEvolucao ? [
                'data' => optional($ultimaEvolucao->finalizado_em ?? $ultimaEvolucao->created_at)->format('d/m/Y H:i'),
                'profissional' => $ultimaEvolucao->profissional?->name,
            ] : null,
            'pendencias' => [
                'evolucoes' => $pendenciasEvolucao,
                'avaliacoes' => $pendenciasAvaliacao,
            ],
        ];
    }

    /**
     * Abre o formulário para criar adendo
     */
    public function abrirModalAdendo($evolucaoPaiId)
    {
        return redirect()->route('app.evolucoes.create', [
            'evolucao_pai_id' => $evolucaoPaiId,
            'paciente_id' => $this->pacienteId,
        ]);
    }

    /**
     * Abre modal de upload de documento
     */
    public function abrirModalUpload()
    {
        // Verifica permissão
        if (!Auth::user()->can('upload_documento') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria'])) {
            session()->flash('error', 'Você não tem permissão para fazer upload de documentos.');
            $this->enviarToast('Sem permissão para enviar documentos.', 'error');
            return;
        }

        $this->mostrarModalUpload = true;
        $this->reset(['titulo_documento', 'categoria', 'arquivo']);
    }

    /**
     * Fecha modal de upload
     */
    public function fecharModalUpload()
    {
        $this->mostrarModalUpload = false;
        $this->reset(['titulo_documento', 'categoria', 'arquivo']);
    }

    /**
     * Salva documento
     */
    public function salvarDocumento()
    {
        // Verifica permissão
        if (!Auth::user()->can('upload_documento') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria'])) {
            session()->flash('error', 'Você não tem permissão para fazer upload de documentos.');
            $this->enviarToast('Sem permissão para enviar documentos.', 'error');
            return;
        }

        // Validação
        $this->validate([
            'titulo_documento' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'arquivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ], [
            'titulo_documento.required' => 'O título do documento é obrigatório.',
            'arquivo.required' => 'É necessário selecionar um arquivo.',
            'arquivo.mimes' => 'O arquivo deve ser PDF ou imagem (JPG, PNG).',
            'arquivo.max' => 'O arquivo não pode ser maior que 10MB.',
        ]);

        try {
            // Faz upload do arquivo
            $path = $this->arquivo->store('documentos-pacientes', 'public');

            // Cria documento
            Documento::create([
                'paciente_id' => $this->pacienteId,
                'user_id' => Auth::id(),
                'titulo_documento' => $this->titulo_documento,
                'path_arquivo' => $path,
                'categoria' => $this->categoria ?: null,
            ]);

            // Recarrega paciente e linha do tempo
            $this->paciente->refresh();
            $this->carregarLinhaTempo();

            $this->fecharModalUpload();
            session()->flash('message', 'Documento enviado com sucesso!');
            $this->enviarToast('Documento enviado com sucesso!', 'success');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao enviar documento: ' . $e->getMessage());
            $this->enviarToast('Erro ao enviar documento.', 'error');
        }
    }

    /**
     * Abre modal de confirmação para deletar documento
     */
    public function confirmarDeletarDocumento($documentoId)
    {
        $documento = Documento::findOrFail($documentoId);

        // Verifica permissão
        if (!Auth::user()->can('apagar_documento') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            session()->flash('error', 'Você não tem permissão para deletar documentos.');
            $this->enviarToast('Sem permissão para deletar documentos.', 'error');
            return;
        }

        // Verifica se o documento pertence ao paciente
        if ($documento->paciente_id !== $this->pacienteId) {
            session()->flash('error', 'Documento não encontrado.');
            $this->enviarToast('Documento não encontrado.', 'error');
            return;
        }

        $this->documentoParaDeletar = $documento;
        $this->mostrarModalConfirmacao = true;
    }

    /**
     * Deleta documento
     */
    public function deletarDocumento()
    {
        if (!$this->documentoParaDeletar) {
            return;
        }

        // Verifica permissão
        if (!Auth::user()->can('apagar_documento') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            session()->flash('error', 'Você não tem permissão para deletar documentos.');
            $this->fecharModalConfirmacao();
            return;
        }

        try {
            // Deleta arquivo do storage
            if (Storage::disk('public')->exists($this->documentoParaDeletar->path_arquivo)) {
                Storage::disk('public')->delete($this->documentoParaDeletar->path_arquivo);
            }

            // Deleta registro
            $this->documentoParaDeletar->delete();

            // Recarrega paciente e linha do tempo
            $this->paciente->refresh();
            $this->carregarLinhaTempo();

            $this->fecharModalConfirmacao();
            session()->flash('message', 'Documento deletado com sucesso!');
            $this->enviarToast('Documento removido.', 'success');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao deletar documento: ' . $e->getMessage());
            $this->enviarToast('Erro ao deletar documento.', 'error');
        }
    }

    /**
     * Fecha modal de confirmação
     */
    public function fecharModalConfirmacao()
    {
        $this->mostrarModalConfirmacao = false;
        $this->documentoParaDeletar = null;
    }

    public function exportarLinhaTempo(): StreamedResponse
    {
        $this->carregarLinhaTempo();

        $filename = 'prontuario-' . Str::slug($this->paciente->nome_completo) . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Data', 'Tipo', 'Título', 'Detalhes', 'Status']);

            foreach ($this->linhaTempo as $item) {
                $data = $item['data'] instanceof \DateTimeInterface
                    ? $item['data']->format('d/m/Y H:i')
                    : (string) $item['data'];

                fputcsv($handle, [
                    $data,
                    ucfirst($item['tipo']),
                    $item['titulo'] ?? '',
                    $item['subtitulo'] ?? '',
                    $item['status'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function enviarToast(string $mensagem, string $tipo = 'info'): void
    {
        $this->dispatch('app:toast', message: $mensagem, type: $tipo);
    }

    public function render()
    {
        return view('livewire.prontuario-view');
    }
}
