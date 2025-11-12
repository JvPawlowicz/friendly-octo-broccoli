<?php

namespace App\Livewire;

use App\Events\AtendimentoAtualizado;
use App\Events\AtendimentoConcluido;
use App\Models\Atendimento;
use App\Models\Unidade;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AgendaBoard extends Component
{
    public string $dataConsulta;
    public ?int $unidadeId = null;

    /** @var array<string, array<int, array<string,mixed>>> */
    public array $colunas = [];

    /** @var array<string, int> */
    public array $metricas = [];

    protected array $statusOrdenacao = ['Agendado', 'Confirmado', 'Check-in', 'Concluído', 'Cancelado'];

    public function mount(): void
    {
        if (!Auth::user()->can('ver_agenda_unidade') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria'])) {
            abort(403, 'Você não tem permissão para acessar esta visão.');
        }

        $this->dataConsulta = now()->format('Y-m-d');

        $user = Auth::user();
        if (!$user->hasRole('Admin')) {
            $unidades = $user->unidades;
            if ($unidades->count() === 1) {
                $this->unidadeId = $unidades->first()->id;
            } else {
                $selecionada = session('unidade_selecionada');
                if ($selecionada && $unidades->contains('id', $selecionada)) {
                    $this->unidadeId = $selecionada;
                }
            }
        } else {
            $selecionada = session('unidade_selecionada');
            if ($selecionada) {
                $this->unidadeId = $selecionada;
            }
        }

        $this->carregarQuadro();
    }

    public function updatedUnidadeId(): void
    {
        if ($this->unidadeId) {
            session(['unidade_selecionada' => $this->unidadeId]);
        } else {
            session()->forget('unidade_selecionada');
        }

        $this->carregarQuadro();
        $this->toast('Filtro de unidade atualizado.', 'info');
    }

    public function updatedDataConsulta(): void
    {
        $this->carregarQuadro();
    }

    public function diaAnterior(): void
    {
        $this->dataConsulta = Carbon::parse($this->dataConsulta)->subDay()->format('Y-m-d');
        $this->carregarQuadro();
    }

    public function proximoDia(): void
    {
        $this->dataConsulta = Carbon::parse($this->dataConsulta)->addDay()->format('Y-m-d');
        $this->carregarQuadro();
    }

    public function hoje(): void
    {
        $this->dataConsulta = now()->format('Y-m-d');
        $this->carregarQuadro();
    }

    public function moverStatus(int $atendimentoId, string $novoStatus): void
    {
        $atendimento = Atendimento::with(['paciente', 'profissional'])->findOrFail($atendimentoId);

        if (!Auth::user()->can('editar_agenda_unidade') && $atendimento->user_id !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            $this->toast('Você não tem permissão para alterar este atendimento.', 'error');
            return;
        }

        $atendimento->update(['status' => $novoStatus]);

        event(new AtendimentoAtualizado($atendimento));

        if ($novoStatus === 'Concluído') {
            event(new AtendimentoConcluido($atendimento));
        }

        $this->carregarQuadro();
        $this->toast('Status atualizado para ' . $novoStatus . '.', 'success');
    }

    public function render()
    {
        $unidades = Auth::user()->hasRole('Admin')
            ? Unidade::orderBy('nome')->get()
            : Auth::user()->unidades()->orderBy('nome')->get();

        return view('livewire.agenda-board', [
            'unidades' => $unidades,
            'statusOrdenacao' => $this->statusOrdenacao,
        ]);
    }

    protected function carregarQuadro(): void
    {
        $data = Carbon::parse($this->dataConsulta);

        $query = Atendimento::with(['paciente', 'profissional', 'sala'])
            ->whereDate('data_hora_inicio', $data);

        $user = Auth::user();

        if ($this->unidadeId) {
            $query->whereHas('sala', fn ($q) => $q->where('unidade_id', $this->unidadeId));
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            if (!empty($unidadeIds)) {
                $query->whereHas('sala', fn ($q) => $q->whereIn('unidade_id', $unidadeIds));
            }
        }

        $atendimentos = $query->orderBy('data_hora_inicio')->get();

        $this->colunas = [];
        $this->metricas = [];

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
        }
    }

    protected function toast(string $mensagem, string $tipo = 'info'): void
    {
        $this->dispatch('app:toast', message: $mensagem, type: $tipo);
    }
}
