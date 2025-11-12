<?php

namespace App\Livewire;

use App\Livewire\Concerns\HandlesFavorites;
use App\Models\Atendimento;
use App\Models\BloqueioAgenda;
use App\Models\Paciente;
use App\Models\Sala;
use App\Models\Unidade;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class AgendaView extends Component
{
    use HandlesFavorites;

    // Filtros
    public ?int $unidadeId = null;
    public ?int $userId = null;
    public ?int $salaId = null;
    public ?string $statusFiltro = null;
    public $favoriteSelecionado = null;

    // Estado dos modais
    public bool $mostrarModal = false;
    public bool $mostrarModalStatus = false;
    public ?int $atendimentoId = null;
    public ?int $atendimentoIdStatus = null;

    // Eventos para o FullCalendar
    public $eventos = [];
    public array $canaisAgenda = [];

    // Painel lateral de detalhes
    public bool $mostrarPainelDetalhe = false;
    public ?array $atendimentoSelecionado = null;

    // Resumo de métricas
    public array $metricas = [
        'agendados' => 0,
        'confirmados' => 0,
        'checkin' => 0,
        'cancelados' => 0,
    ];

    protected string $filtrosSessionKey = 'agenda_filtros';

    protected $listeners = [
        'refreshAgenda' => 'atualizarAgenda',
        'fecharPainelDetalhe' => 'fecharPainelDetalhe',
        'fechar-modal-atendimento' => 'fecharModal',
        'fechar-modal-status' => 'fecharModalStatus',
        'atendimento-salvo' => 'atualizarAgenda',
        'status-atualizado' => 'atualizarAgenda',
    ];

    public function mount()
    {
        // Verifica permissão - Admin tem acesso total
        if (!Auth::user()->can('ver_agenda_unidade') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para acessar a agenda.');
        }
        
        // Define unidade padrão
        $user = Auth::user();
        if (!$user->hasRole('Admin')) {
            $unidades = $user->unidades;
            if ($unidades->count() === 1) {
                $this->unidadeId = $unidades->first()->id;
                session(['unidade_selecionada' => $this->unidadeId]);
            } else {
                // Se há unidade selecionada na sessão, usa ela
                $unidadeSelecionada = session('unidade_selecionada');
                if ($unidadeSelecionada && $unidades->contains('id', $unidadeSelecionada)) {
                    $this->unidadeId = $unidadeSelecionada;
                }
            }
        } else {
            // Admin pode ter unidade selecionada na sessão
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $this->unidadeId = $unidadeSelecionada;
            }
        }

        $this->carregarFiltrosPersistidos();
        $this->carregarEventos();
        $this->atualizarCanaisAgenda();
        $this->carregarFavoritos();
    }

    public function updatedUnidadeId()
    {
        // Salva a unidade selecionada na sessão
        if ($this->unidadeId) {
            session(['unidade_selecionada' => $this->unidadeId]);
        } else {
            session()->forget('unidade_selecionada');
        }

        $this->persistirFiltros();
        
        $this->salaId = null; // Reseta sala quando muda unidade
        $this->carregarEventos();
        $this->atualizarCanaisAgenda();
        $this->enviarToast($this->unidadeId ? 'Unidade filtrada.' : 'Filtro de unidade removido.', 'info');
    }

    public function updatedUserId()
    {
        $this->carregarEventos();
        $this->persistirFiltros();
        if ($this->userId) {
            $this->enviarToast('Profissional filtrado.', 'info');
        } else {
            $this->enviarToast('Filtro de profissional removido.', 'info');
        }
    }

    public function updatedSalaId()
    {
        $this->carregarEventos();
        $this->persistirFiltros();
        if ($this->salaId) {
            $this->enviarToast('Sala filtrada.', 'info');
        } else {
            $this->enviarToast('Filtro de sala removido.', 'info');
        }
    }

    public function updatedStatusFiltro()
    {
        $this->carregarEventos();
        $this->persistirFiltros();
        if ($this->statusFiltro) {
            $this->enviarToast('Status filtrado: ' . $this->statusFiltro, 'info');
        } else {
            $this->enviarToast('Filtro de status removido.', 'info');
        }
    }

    // Método chamado automaticamente quando wire:model.live atualiza
    // Não precisa mais de métodos separados, o Livewire já atualiza automaticamente

    public function carregarEventos()
    {
        $this->eventos = [];

        // Carrega Atendimentos
        $queryAtendimentos = Atendimento::with(['paciente', 'profissional', 'sala.unidade'])
            ->whereBetween('data_hora_inicio', [
                now()->startOfMonth()->format('Y-m-d H:i:s'),
                now()->endOfMonth()->addMonths(1)->format('Y-m-d H:i:s'),
            ]);

        $user = Auth::user();
        
        // Se for Profissional, mostra apenas seus atendimentos
        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $queryAtendimentos->where('user_id', $user->id);
            session()->forget('unidade_selecionada');
            $this->unidadeId = null;
        } else {
            // Para outras roles, filtra por unidade
            if ($this->unidadeId) {
                $queryAtendimentos->whereHas('sala', function ($q) {
                    $q->where('unidade_id', $this->unidadeId);
                });
            } else {
                // Se não há unidade selecionada, filtra pelas unidades do usuário
                if (!$user->hasRole('Admin')) {
                    $unidadeIds = $user->unidades->pluck('id')->toArray();
                    $unidadeSelecionada = session('unidade_selecionada');

                    if ($unidadeSelecionada && in_array($unidadeSelecionada, $unidadeIds)) {
                        $queryAtendimentos->whereHas('sala', function ($q) use ($unidadeSelecionada) {
                            $q->where('unidade_id', $unidadeSelecionada);
                        });
                    } else {
                        $queryAtendimentos->whereHas('sala', function ($q) use ($unidadeIds) {
                            $q->whereIn('unidade_id', $unidadeIds);
                        });
                    }
                }
            }
        }

        // Filtros adicionais
        if ($this->userId) {
            $queryAtendimentos->where('user_id', $this->userId);
        }

        if ($this->salaId) {
            $queryAtendimentos->where('sala_id', $this->salaId);
        }

        if ($this->statusFiltro) {
            $queryAtendimentos->where('status', $this->statusFiltro);
        }

        $contadores = [];
        $atendimentos = $queryAtendimentos->get();

        foreach ($atendimentos as $atendimento) {
            $contadores[$atendimento->status] = ($contadores[$atendimento->status] ?? 0) + 1;

            $tituloEvento = sprintf('%s • %s', $atendimento->paciente->nome_completo, $atendimento->profissional->name);

            $this->eventos[] = [
                'id' => 'atendimento_' . $atendimento->id,
                'title' => $tituloEvento,
                'start' => $atendimento->data_hora_inicio->toIso8601String(),
                'end' => $atendimento->data_hora_fim->toIso8601String(),
                'backgroundColor' => $this->getCorStatus($atendimento->status),
                'borderColor' => $this->getCorStatus($atendimento->status),
                'extendedProps' => [
                    'tipo' => 'atendimento',
                    'atendimento_id' => $atendimento->id,
                    'status' => $atendimento->status,
                    'profissional' => $atendimento->profissional->name,
                    'profissional_email' => $atendimento->profissional->email,
                    'sala' => $atendimento->sala?->nome,
                    'unidade' => $atendimento->sala?->unidade?->nome,
                    'paciente' => $atendimento->paciente->nome_completo,
                    'paciente_telefone' => $atendimento->paciente->telefone_principal,
                    'paciente_email' => $atendimento->paciente->email_principal,
                    'inicio_legivel' => $atendimento->data_hora_inicio->format('d/m/Y H:i'),
                    'fim_legivel' => $atendimento->data_hora_fim->format('d/m/Y H:i'),
                ],
            ];
        }

        // Carrega Bloqueios
        $queryBloqueios = BloqueioAgenda::query()
            ->whereBetween('data_hora_inicio', [
                now()->startOfMonth()->format('Y-m-d H:i:s'),
                now()->endOfMonth()->addMonths(1)->format('Y-m-d H:i:s'),
            ]);

        if ($this->unidadeId) {
            $queryBloqueios->where(function ($q) {
                $q->where('unidade_id', $this->unidadeId)
                  ->orWhereNull('unidade_id'); // Bloqueios globais
            });
        }

        if ($this->userId) {
            $queryBloqueios->where(function ($q) {
                $q->where('user_id', $this->userId)
                  ->orWhereNull('user_id');
            });
        }

        if ($this->salaId) {
            $queryBloqueios->where(function ($q) {
                $q->where('sala_id', $this->salaId)
                  ->orWhereNull('sala_id');
            });
        }

        foreach ($queryBloqueios->get() as $bloqueio) {
            $this->eventos[] = [
                'id' => 'bloqueio_' . $bloqueio->id,
                'title' => $bloqueio->titulo_bloqueio,
                'start' => $bloqueio->data_hora_inicio->toIso8601String(),
                'end' => $bloqueio->data_hora_fim->toIso8601String(),
                'backgroundColor' => '#dc2626',
                'borderColor' => '#dc2626',
                'display' => 'background',
                'extendedProps' => [
                    'tipo' => 'bloqueio',
                    'bloqueio_id' => $bloqueio->id,
                ],
            ];
        }

        $this->dispatch('calendar-update', eventos: $this->eventos);
        $this->atualizarMetricas($contadores);
        $this->atualizarCanaisAgenda();
    }

    protected function atualizarMetricas(array $contadores): void
    {
        $this->metricas = [
            'agendados' => $contadores['Agendado'] ?? 0,
            'confirmados' => $contadores['Confirmado'] ?? 0,
            'checkin' => $contadores['Check-in'] ?? 0,
            'cancelados' => $contadores['Cancelado'] ?? 0,
        ];
    }

    private function getCorStatus(string $status): string
    {
        return match ($status) {
            'Agendado' => '#3b82f6',
            'Confirmado' => '#10b981',
            'Check-in' => '#f59e0b',
            'Concluído' => '#6366f1',
            'Cancelado' => '#ef4444',
            default => '#6b7280',
        };
    }

    protected function persistirFiltros(): void
    {
        $dados = array_filter([
            'unidade_id' => $this->unidadeId,
            'user_id' => $this->userId,
            'sala_id' => $this->salaId,
            'status' => $this->statusFiltro,
        ], fn ($valor) => !is_null($valor));

        if (empty($dados)) {
            session()->forget($this->filtrosSessionKey);
            return;
        }

        session([$this->filtrosSessionKey => $dados]);
    }

    protected function carregarFiltrosPersistidos(): void
    {
        $filtrosPersistidos = session($this->filtrosSessionKey, []);

        if (isset($filtrosPersistidos['unidade_id'])) {
            $this->unidadeId = (int) $filtrosPersistidos['unidade_id'];
        }

        if (isset($filtrosPersistidos['user_id'])) {
            $this->userId = (int) $filtrosPersistidos['user_id'];
        }

        if (isset($filtrosPersistidos['sala_id'])) {
            $this->salaId = (int) $filtrosPersistidos['sala_id'];
        }

        if (isset($filtrosPersistidos['status'])) {
            $this->statusFiltro = $filtrosPersistidos['status'];
        }
    }

    public function abrirModal($atendimentoId = null, $dataInicio = null)
    {
        $this->atendimentoId = $atendimentoId;
        $this->mostrarModal = true;
        $this->dispatch('abrir-modal-atendimento', atendimentoId: $atendimentoId, dataInicio: $dataInicio);
    }

    public function abrirModalStatus($atendimentoId)
    {
        $this->atendimentoIdStatus = $atendimentoId;
        $this->mostrarModalStatus = true;
    }

    public function fecharModal()
    {
        $this->mostrarModal = false;
        $this->atendimentoId = null;
        $this->carregarEventos();
    }

    public function fecharModalStatus()
    {
        $this->mostrarModalStatus = false;
        $this->atendimentoIdStatus = null;
        $this->carregarEventos();
    }

    public function atualizarStatus($atendimentoId, $novoStatus)
    {
        $atendimento = Atendimento::findOrFail($atendimentoId);
        
        // Verifica permissão
        $user = Auth::user();
        if (!$user->can('editar_agenda_unidade') && 
            $atendimento->user_id !== $user->id && 
            !$user->hasRole('Admin')) {
            session()->flash('error', 'Você não tem permissão para alterar este atendimento.');
            return;
        }
        
        $atendimento->update(['status' => $novoStatus]);

        // Dispara eventos para broadcast
        event(new \App\Events\AtendimentoAtualizado($atendimento));
        
        if ($novoStatus === 'Concluído') {
            event(new \App\Events\AtendimentoConcluido($atendimento));
        }

        $this->carregarEventos();
        session()->flash('message', 'Status atualizado com sucesso!');
        $this->enviarToast('Status atualizado com sucesso!', 'success');
    }

    public function atualizarAgenda()
    {
        // Método chamado via Livewire quando recebe evento do Echo
        $this->carregarEventos();
    }

    public function atualizarDataAtendimento($atendimentoId, $novaData)
    {
        $atendimento = Atendimento::findOrFail($atendimentoId);
        $inicio = Carbon::parse($novaData);
        $fim = $inicio->copy()->addMinutes($atendimento->data_hora_inicio->diffInMinutes($atendimento->data_hora_fim));

        if ($mensagem = $this->verificarConflitoArraste($atendimento, $inicio, $fim)) {
            $this->dispatch('agenda-conflito', message: $mensagem);
            return [
                'conflict' => true,
                'message' => $mensagem,
            ];
        }
        
        $atendimento->update([
            'data_hora_inicio' => $inicio,
            'data_hora_fim' => $fim,
        ]);

        event(new \App\Events\AtendimentoAtualizado($atendimento));
        $this->carregarEventos();

        return ['success' => true];
    }

    public function atualizarDuracaoAtendimento($atendimentoId, $novaDataInicio, $novaDataFim)
    {
        $atendimento = Atendimento::findOrFail($atendimentoId);
        $inicio = Carbon::parse($novaDataInicio);
        $fim = Carbon::parse($novaDataFim);

        if ($mensagem = $this->verificarConflitoArraste($atendimento, $inicio, $fim)) {
            $this->dispatch('agenda-conflito', message: $mensagem);
            return [
                'conflict' => true,
                'message' => $mensagem,
            ];
        }

        $atendimento->update([
            'data_hora_inicio' => $inicio,
            'data_hora_fim' => $fim,
        ]);

        event(new \App\Events\AtendimentoAtualizado($atendimento));
        $this->carregarEventos();

        return ['success' => true];
    }

    public function mostrarResumo(int $atendimentoId): void
    {
        $atendimento = Atendimento::with(['paciente', 'profissional', 'sala.unidade'])
            ->findOrFail($atendimentoId);

        $this->atendimentoSelecionado = [
            'id' => $atendimento->id,
            'status' => $atendimento->status,
            'status_cor' => $this->getCorStatus($atendimento->status),
            'paciente' => [
                'nome' => $atendimento->paciente->nome_completo,
                'telefone' => $atendimento->paciente->telefone_principal,
                'email' => $atendimento->paciente->email_principal,
            ],
            'profissional' => [
                'nome' => $atendimento->profissional->name,
                'email' => $atendimento->profissional->email,
            ],
            'sala' => $atendimento->sala?->nome,
            'unidade' => $atendimento->sala?->unidade?->nome,
            'inicio' => $atendimento->data_hora_inicio->translatedFormat('d \\d\\e F \\à\\s H:i'),
            'fim' => $atendimento->data_hora_fim->translatedFormat('d \\d\\e F \\à\\s H:i'),
            'duracao' => $atendimento->data_hora_inicio->diffInMinutes($atendimento->data_hora_fim),
        ];

        $this->mostrarPainelDetalhe = true;
    }

    public function fecharPainelDetalhe(): void
    {
        $this->mostrarPainelDetalhe = false;
        $this->atendimentoSelecionado = null;
    }

    private function verificarConflitoArraste(Atendimento $atendimento, Carbon $inicio, Carbon $fim): ?string
    {
        $queryBase = Atendimento::where('id', '!=', $atendimento->id)
            ->where('status', '!=', 'Cancelado')
            ->where(function ($q) use ($inicio, $fim) {
                $q->whereBetween('data_hora_inicio', [$inicio, $fim])
                  ->orWhereBetween('data_hora_fim', [$inicio, $fim])
                  ->orWhere(function ($q2) use ($inicio, $fim) {
                      $q2->where('data_hora_inicio', '<=', $inicio)
                         ->where('data_hora_fim', '>=', $fim);
                  });
            });

        if ($atendimento->user_id) {
            $conflitoProfissional = (clone $queryBase)->where('user_id', $atendimento->user_id)->first();
            if ($conflitoProfissional) {
                return "Profissional já possui atendimento neste horário com {$conflitoProfissional->paciente->nome_completo}";
            }
        }

        if ($atendimento->sala_id) {
            $conflitoSala = (clone $queryBase)->where('sala_id', $atendimento->sala_id)->first();
            if ($conflitoSala) {
                return "Sala já ocupada por {$conflitoSala->paciente->nome_completo}";
            }
        }

        $conflitoPaciente = (clone $queryBase)->where('paciente_id', $atendimento->paciente_id)->first();
        if ($conflitoPaciente) {
            return "Paciente já possui atendimento neste horário";
        }

        $bloqueio = BloqueioAgenda::where(function ($q) use ($inicio, $fim) {
            $q->whereBetween('data_hora_inicio', [$inicio, $fim])
              ->orWhereBetween('data_hora_fim', [$inicio, $fim])
              ->orWhere(function ($q2) use ($inicio, $fim) {
                  $q2->where('data_hora_inicio', '<=', $inicio)
                     ->where('data_hora_fim', '>=', $fim);
              });
        })
        ->where(function ($q) use ($atendimento) {
            $q->whereNull('user_id')->orWhere('user_id', $atendimento->user_id);
        })
        ->where(function ($q) use ($atendimento) {
            $q->whereNull('sala_id')->orWhere('sala_id', $atendimento->sala_id);
        })
        ->first();

        if ($bloqueio) {
            return "Horário bloqueado ({$bloqueio->titulo_bloqueio})";
        }

        return null;
    }

    private function determinarCanais(): array
    {
        $user = Auth::user();

        if ($this->unidadeId) {
            return [$this->unidadeId];
        }

        if ($user->hasRole('Admin')) {
            return Unidade::pluck('id')->toArray();
        }

        return $user->unidades->pluck('id')->toArray();
    }

    private function atualizarCanaisAgenda(): void
    {
        $novosCanais = $this->determinarCanais();
        sort($novosCanais);

        $atual = $this->canaisAgenda;
        sort($atual);

        if ($atual !== $novosCanais) {
            $this->canaisAgenda = $novosCanais;
            $this->dispatch('agenda-canais', canais: $this->canaisAgenda);
        }
    }

    protected function enviarToast(string $mensagem, string $tipo = 'info'): void
    {
        $this->dispatch('app:toast', message: $mensagem, type: $tipo);
    }

    protected function favoriteContext(): string
    {
        return 'agenda';
    }

    protected function favoritePayload(): array
    {
        return [
            'unidadeId' => $this->unidadeId,
            'userId' => $this->userId,
            'salaId' => $this->salaId,
            'statusFiltro' => $this->statusFiltro,
        ];
    }

    protected function applyFavoritePayload(array $payload): void
    {
        $this->unidadeId = $payload['unidadeId'] ?? null;
        $this->userId = $payload['userId'] ?? null;
        $this->salaId = $payload['salaId'] ?? null;
        $this->statusFiltro = $payload['statusFiltro'] ?? null;

        $this->persistirFiltros();
        $this->carregarEventos();
        $this->atualizarCanaisAgenda();
    }

    public function render()
    {
        $this->carregarEventos();
        $this->atualizarCanaisAgenda();

        $unidades = Auth::user()->hasRole('Admin') 
            ? Unidade::orderBy('nome')->get()
            : Auth::user()->unidades;

        $profissionais = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Profissional', 'Coordenador']);
        })->orderBy('name')->get();

        $salas = $this->unidadeId 
            ? Sala::where('unidade_id', $this->unidadeId)->orderBy('nome')->get()
            : collect();

        return view('livewire.agenda-view', [
            'unidades' => $unidades,
            'profissionais' => $profissionais,
            'salas' => $salas,
            'canaisAgenda' => $this->canaisAgenda,
        ]);
    }
}
