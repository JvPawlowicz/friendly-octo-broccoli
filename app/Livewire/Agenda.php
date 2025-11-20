<?php

namespace App\Livewire;

use App\Livewire\Concerns\HandlesFavorites;
use App\Livewire\Concerns\HandlesToasts;
use App\Events\AtendimentoAtualizado;
use App\Events\AtendimentoConcluido;
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
class Agenda extends Component
{
    use HandlesFavorites, HandlesToasts;

    // Modo de visualização: 'calendar' ou 'board'
    public string $viewMode = 'calendar';

    // Filtros
    public ?int $unidadeId = null;
    public ?int $userId = null;
    public ?int $salaId = null;
    public ?string $statusFiltro = null;
    public $favoriteSelecionado = null;

    // Board específico
    public string $dataConsulta;

    // Estado dos modais
    public bool $mostrarModal = false;
    public bool $mostrarModalStatus = false;
    public ?int $atendimentoId = null;
    public ?int $atendimentoIdStatus = null;
    public ?string $dataInicioModal = null;

    // Eventos para o FullCalendar
    public $eventos = [];
    public array $canaisAgenda = [];

    // Board - colunas e métricas
    public array $colunas = [];
    public array $metricas = [];

    // Resumo de métricas (compartilhado)
    public array $metricasResumo = [
        'agendados' => 0,
        'confirmados' => 0,
        'checkin' => 0,
        'cancelados' => 0,
    ];

    protected array $statusOrdenacao = ['Agendado', 'Confirmado', 'Check-in', 'Concluído', 'Cancelado'];
    protected string $filtrosSessionKey = 'agenda_filtros';

    protected $listeners = [
        'refreshAgenda' => 'atualizarAgenda',
        'fechar-modal-atendimento' => 'fecharModal',
        'fechar-modal-status' => 'fecharModalStatus',
        'atendimento-salvo' => 'atualizarAgenda',
        'status-atualizado' => 'atualizarAgenda',
        'abrir-modal-agenda' => 'abrirModalViaEvento',
    ];

    public function mount()
    {
        if (!Auth::user()->can('ver_agenda_unidade') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria'])) {
            abort(403, 'Você não tem permissão para acessar a agenda.');
        }

        $this->dataConsulta = now()->format('Y-m-d');
        $this->inicializarUnidade();
        $this->carregarFiltrosPersistidos();
        $this->carregarDados();
        $this->carregarFavoritos();
    }

    protected function inicializarUnidade()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('Admin')) {
            $unidades = $user->unidades;
            if ($unidades->count() === 1) {
                $this->unidadeId = $unidades->first()->id;
                session(['unidade_selecionada' => $this->unidadeId]);
            } else {
                $unidadeSelecionada = session('unidade_selecionada');
                if ($unidadeSelecionada && $unidades->contains('id', $unidadeSelecionada)) {
                    $this->unidadeId = $unidadeSelecionada;
                }
            }
        } else {
            $unidadeSelecionada = session('unidade_selecionada');
            if ($unidadeSelecionada) {
                $this->unidadeId = $unidadeSelecionada;
            }
        }
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'calendar' ? 'board' : 'calendar';
        $this->carregarDados();
    }

    public function updatedUnidadeId()
    {
        if ($this->unidadeId) {
            session(['unidade_selecionada' => $this->unidadeId]);
        } else {
            session()->forget('unidade_selecionada');
        }
        $this->salaId = null;
        $this->persistirFiltros();
        $this->carregarDados();
        $this->enviarToast('Unidade filtrada.', 'info');
    }

    public function updatedUserId()
    {
        $this->persistirFiltros();
        $this->carregarDados();
        $this->enviarToast('Profissional filtrado.', 'info');
    }

    public function updatedSalaId()
    {
        $this->persistirFiltros();
        $this->carregarDados();
        $this->enviarToast('Sala filtrada.', 'info');
    }

    public function updatedStatusFiltro()
    {
        $this->persistirFiltros();
        $this->carregarDados();
        $this->enviarToast('Status filtrado.', 'info');
    }

    public function updatedDataConsulta()
    {
        if ($this->viewMode === 'board') {
            $this->carregarBoard();
        }
    }

    public function diaAnterior()
    {
        $this->dataConsulta = Carbon::parse($this->dataConsulta)->subDay()->format('Y-m-d');
        $this->carregarBoard();
    }

    public function proximoDia()
    {
        $this->dataConsulta = Carbon::parse($this->dataConsulta)->addDay()->format('Y-m-d');
        $this->carregarBoard();
    }

    public function hoje()
    {
        $this->dataConsulta = now()->format('Y-m-d');
        $this->carregarBoard();
    }

    public function carregarDados()
    {
        if ($this->viewMode === 'calendar') {
            $this->carregarEventos();
        } else {
            $this->carregarBoard();
        }
    }

    protected function carregarEventos()
    {
        $this->eventos = [];

        $queryAtendimentos = Atendimento::with(['paciente', 'profissional', 'sala.unidade'])
            ->whereBetween('data_hora_inicio', [
                now()->startOfMonth()->format('Y-m-d H:i:s'),
                now()->endOfMonth()->addMonths(1)->format('Y-m-d H:i:s'),
            ]);

        $this->aplicarFiltros($queryAtendimentos);

        $contadores = [];
        $atendimentos = $queryAtendimentos->get();

        foreach ($atendimentos as $atendimento) {
            $contadores[$atendimento->status] = ($contadores[$atendimento->status] ?? 0) + 1;

            $this->eventos[] = [
                'id' => 'atendimento_' . $atendimento->id,
                'title' => sprintf('%s • %s', $atendimento->paciente->nome_completo, $atendimento->profissional->name),
                'start' => $atendimento->data_hora_inicio->format('Y-m-d\TH:i:s'),
                'end' => $atendimento->data_hora_fim->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $this->getCorStatus($atendimento->status),
                'borderColor' => $this->getCorStatus($atendimento->status),
                'extendedProps' => [
                    'tipo' => 'atendimento',
                    'atendimento_id' => $atendimento->id,
                    'status' => $atendimento->status,
                    'profissional' => $atendimento->profissional->name,
                    'sala' => $atendimento->sala?->nome,
                    'paciente' => $atendimento->paciente->nome_completo,
                ],
            ];
        }

        // Bloqueios
        $queryBloqueios = BloqueioAgenda::query()
            ->whereBetween('data_hora_inicio', [
                now()->startOfMonth()->format('Y-m-d H:i:s'),
                now()->endOfMonth()->addMonths(1)->format('Y-m-d H:i:s'),
            ]);

        if ($this->unidadeId) {
            $queryBloqueios->where(function ($q) {
                $q->where('unidade_id', $this->unidadeId)->orWhereNull('unidade_id');
            });
        }

        foreach ($queryBloqueios->get() as $bloqueio) {
            $this->eventos[] = [
                'id' => 'bloqueio_' . $bloqueio->id,
                'title' => $bloqueio->titulo_bloqueio,
                'start' => $bloqueio->data_hora_inicio->format('Y-m-d\TH:i:s'),
                'end' => $bloqueio->data_hora_fim->format('Y-m-d\TH:i:s'),
                'backgroundColor' => '#dc2626',
                'borderColor' => '#dc2626',
                'display' => 'background',
            ];
        }

        $this->dispatch('calendar-update', eventos: $this->eventos);
        $this->atualizarMetricas($contadores);
    }

    protected function carregarBoard()
    {
        $data = Carbon::parse($this->dataConsulta);

        $query = Atendimento::with(['paciente', 'profissional', 'sala'])
            ->whereDate('data_hora_inicio', $data);

        $this->aplicarFiltros($query);

        $atendimentos = $query->orderBy('data_hora_inicio')->get();

        $this->colunas = [];
        $this->metricas = [];
        $contadores = [];

        foreach ($this->statusOrdenacao as $status) {
            $lista = $atendimentos->where('status', $status)->map(function (Atendimento $item) {
                return [
                    'id' => $item->id,
                    'horario' => $item->data_hora_inicio->format('H:i'),
                    'paciente' => $item->paciente?->nome_completo ?? 'Paciente',
                    'profissional' => $item->profissional?->name ?? 'N/A',
                    'sala' => $item->sala?->nome,
                    'status' => $item->status,
                ];
            })->values()->all();

            $this->colunas[$status] = $lista;
            $this->metricas[$status] = count($lista);
            $contadores[$status] = count($lista);
        }

        $this->atualizarMetricas($contadores);
    }

    protected function aplicarFiltros($query)
    {
        $user = Auth::user();

        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $query->where('user_id', $user->id);
        } else {
            if ($this->unidadeId) {
                $query->whereHas('sala', function ($q) {
                    $q->where('unidade_id', $this->unidadeId);
                });
            } elseif (!$user->hasRole('Admin')) {
                $unidadeIds = $user->unidades->pluck('id')->toArray();
                if (!empty($unidadeIds)) {
                    $query->whereHas('sala', function ($q) use ($unidadeIds) {
                        $q->whereIn('unidade_id', $unidadeIds);
                    });
                }
            }
        }

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->salaId) {
            $query->where('sala_id', $this->salaId);
        }

        if ($this->statusFiltro) {
            $query->where('status', $this->statusFiltro);
        }
    }

    protected function atualizarMetricas(array $contadores): void
    {
        $this->metricasResumo = [
            'agendados' => $contadores['Agendado'] ?? 0,
            'confirmados' => $contadores['Confirmado'] ?? 0,
            'checkin' => $contadores['Check-in'] ?? 0,
            'cancelados' => $contadores['Cancelado'] ?? 0,
        ];
    }

    public function moverStatus(int $atendimentoId, string $novoStatus): void
    {
        $atendimento = Atendimento::with(['paciente', 'profissional'])->findOrFail($atendimentoId);

        if (!Auth::user()->can('editar_agenda_unidade') && $atendimento->user_id !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            $this->enviarToast('Você não tem permissão para alterar este atendimento.', 'error');
            return;
        }

        $atendimento->update(['status' => $novoStatus]);

        event(new AtendimentoAtualizado($atendimento));

        if ($novoStatus === 'Concluído') {
            event(new AtendimentoConcluido($atendimento));
        }

        $this->carregarDados();
        $this->enviarToast('Status atualizado para ' . $novoStatus . '.', 'success');
    }

    public function abrirModal($atendimentoId = null, $dataInicio = null)
    {
        $this->atendimentoId = $atendimentoId;
        $this->dataInicioModal = $dataInicio;
        $this->mostrarModal = true;
    }

    public function abrirModalViaEvento($atendimentoId = null, $dataInicio = null)
    {
        // Recebe dados do evento Livewire
        if (is_array($atendimentoId)) {
            $data = $atendimentoId;
            $atendimentoId = $data['atendimentoId'] ?? null;
            $dataInicio = $data['dataInicio'] ?? $data['dataInicio'] ?? null;
        }
        $this->abrirModal($atendimentoId, $dataInicio);
    }
    
    public function atualizarAgenda()
    {
        $this->carregarDados();
    }

    public function fecharModal()
    {
        $this->mostrarModal = false;
        $this->atendimentoId = null;
        $this->dataInicioModal = null;
    }

    public function abrirModalStatus($atendimentoId)
    {
        $this->atendimentoIdStatus = $atendimentoId;
        $this->mostrarModalStatus = true;
    }

    public function fecharModalStatus()
    {
        $this->mostrarModalStatus = false;
        $this->atendimentoIdStatus = null;
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

        $this->unidadeId = $filtrosPersistidos['unidade_id'] ?? $this->unidadeId;
        $this->userId = $filtrosPersistidos['user_id'] ?? null;
        $this->salaId = $filtrosPersistidos['sala_id'] ?? null;
        $this->statusFiltro = $filtrosPersistidos['status'] ?? null;
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


    protected function favoriteContext(): string
    {
        return 'agenda_' . $this->viewMode;
    }

    protected function favoritePayload(): array
    {
        return [
            'view_mode' => $this->viewMode,
            'unidade_id' => $this->unidadeId,
            'user_id' => $this->userId,
            'sala_id' => $this->salaId,
            'status_filtro' => $this->statusFiltro,
            'data_consulta' => $this->dataConsulta ?? now()->format('Y-m-d'),
        ];
    }

    protected function applyFavoritePayload(array $payload): void
    {
        $this->viewMode = $payload['view_mode'] ?? 'calendar';
        $this->unidadeId = $payload['unidade_id'] ?? null;
        $this->userId = $payload['user_id'] ?? null;
        $this->salaId = $payload['sala_id'] ?? null;
        $this->statusFiltro = $payload['status_filtro'] ?? null;
        $this->dataConsulta = $payload['data_consulta'] ?? now()->format('Y-m-d');
        
        if ($this->unidadeId) {
            session(['unidade_selecionada' => $this->unidadeId]);
        }
        
        $this->carregarDados();
    }

    public function render()
    {
        $unidades = Auth::user()->hasRole('Admin')
            ? Unidade::orderBy('nome')->get()
            : Auth::user()->unidades()->orderBy('nome')->get();

        // Inclui Profissionais e Admins que tenham disponibilidade cadastrada
        $profissionais = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Profissional', 'Admin']);
        })
        ->whereHas('disponibilidades') // Apenas quem tem disponibilidade cadastrada
        ->orderBy('name')
        ->get();

        $salas = $this->unidadeId
            ? Sala::where('unidade_id', $this->unidadeId)->orderBy('nome')->get()
            : collect();

        return view('livewire.agenda', [
            'unidades' => $unidades,
            'profissionais' => $profissionais,
            'salas' => $salas,
            'statusOrdenacao' => $this->statusOrdenacao,
        ]);
    }
}

