<?php

namespace App\Livewire;

use App\Livewire\Concerns\HandlesFavorites;
use App\Models\Atendimento;
use App\Models\User;
use App\Models\Unidade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RelatorioProdutividade extends Component
{
    use HandlesFavorites;

    public string $dataInicio;
    public string $dataFim;
    public ?int $profissionalId = null;
    public ?int $unidadeId = null;
    public $favoriteSelecionado = null;

    public array $dados = [];
    public int $totalAtendimentos = 0;
    public int $totalCancelados = 0;

    public array $serieProdutividade = [
        'labels' => [],
        'totais' => [],
        'medias' => [],
    ];

    public array $serieOcupacao = [
        'labels' => [],
        'valores' => [],
    ];

    public array $serieAbsenteismo = [
        'labels' => ['Realizados', 'Cancelados'],
        'valores' => [0, 0],
    ];

    public float $percentualAbsenteismo = 0.0;

    public function mount(): void
    {
        if (!Auth::user()->can('ver_relatorios') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para acessar relatórios.');
        }

        $this->dataInicio = now()->subDays(30)->format('Y-m-d');
        $this->dataFim = now()->format('Y-m-d');
        $this->gerarRelatorio();
        $this->carregarFavoritos();
    }

    public function exportarCsv()
    {
        if (!Auth::user()->can('exportar_relatorios') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            session()->flash('error', 'Você não tem permissão para exportar relatórios.');
            return null;
        }

        $this->gerarRelatorio();
        $this->carregarFavoritos();

        $filename = 'relatorio_produtividade_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Profissional', 'Total de Atendimentos', 'Dias Trabalhados', 'Média Diária'], ';');

            foreach ($this->dados as $item) {
                fputcsv($handle, [
                    $item['profissional_nome'],
                    $item['total'],
                    $item['dias_trabalhados'],
                    number_format($item['media_diaria'], 2, ',', '.'),
                ], ';');
            }

            fputcsv($handle, [] , ';');
            fputcsv($handle, ['TOTAL CONCLUÍDOS', $this->totalAtendimentos, '', ''], ';');
            fputcsv($handle, ['TOTAL CANCELADOS', $this->totalCancelados, '', ''], ';');
            fputcsv($handle, ['Percentual de absenteísmo', $this->percentualAbsenteismo . '%', '', ''], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportarPdf()
    {
        if (!Auth::user()->can('exportar_relatorios') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            session()->flash('error', 'Você não tem permissão para exportar relatórios.');
            return null;
        }

        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            session()->flash('error', 'Para exportar em PDF instale o pacote barryvdh/laravel-dompdf.');
            return null;
        }

        $this->gerarRelatorio();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.relatorio-produtividade', [
            'dados' => $this->dados,
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
            'totalAtendimentos' => $this->totalAtendimentos,
            'totalCancelados' => $this->totalCancelados,
            'percentualAbsenteismo' => $this->percentualAbsenteismo,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('relatorio_produtividade_' . now()->format('Y-m-d_His') . '.pdf');
    }

    public function gerarRelatorio(): void
    {
        $periodoInicio = $this->dataInicio . ' 00:00:00';
        $periodoFim = $this->dataFim . ' 23:59:59';

        $queryBase = Atendimento::query()
            ->whereBetween('data_hora_inicio', [$periodoInicio, $periodoFim])
            ->with(['profissional', 'sala.unidade', 'paciente']);

        $this->aplicarFiltros($queryBase);

        $atendimentosConcluidos = (clone $queryBase)->where('status', 'Concluído')->get();
        $this->totalAtendimentos = $atendimentosConcluidos->count();

        $this->dados = $atendimentosConcluidos->groupBy('user_id')->map(function ($grupo) {
            $profissional = $grupo->first()->profissional;
            $total = $grupo->count();

            $porDia = $grupo->groupBy(function ($atendimento) {
                return $atendimento->data_hora_inicio->format('Y-m-d');
            })->map->count();

            return [
                'profissional_id' => $profissional->id,
                'profissional_nome' => $profissional->name,
                'total' => $total,
                'dias_trabalhados' => $porDia->count(),
                'media_diaria' => $porDia->count() > 0 ? round($total / $porDia->count(), 2) : 0,
                'por_dia' => $porDia->map(function ($quantidade, $data) {
                    return [
                        'data' => Carbon::parse($data)->format('d/m'),
                        'total' => $quantidade,
                    ];
                })->values()->toArray(),
            ];
        })->sortByDesc('total')->values()->toArray();

        $this->serieProdutividade = [
            'labels' => collect($this->dados)->pluck('profissional_nome')->toArray(),
            'totais' => collect($this->dados)->pluck('total')->toArray(),
            'medias' => collect($this->dados)->pluck('media_diaria')->toArray(),
        ];

        $ocupacao = (clone $queryBase)
            ->where('status', '!=', 'Cancelado')
            ->leftJoin('salas', 'salas.id', '=', 'atendimentos.sala_id')
            ->selectRaw('COALESCE(salas.nome, "Sem sala definida") as sala_nome, COUNT(*) as total')
            ->groupBy('sala_nome')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $this->serieOcupacao = [
            'labels' => $ocupacao->pluck('sala_nome')->toArray(),
            'valores' => $ocupacao->pluck('total')->toArray(),
        ];

        $totalAgendados = (clone $queryBase)->count();
        $this->totalCancelados = (clone $queryBase)->where('status', 'Cancelado')->count();
        $realizados = max($totalAgendados - $this->totalCancelados, 0);

        $this->serieAbsenteismo['valores'] = [$realizados, $this->totalCancelados];
        $this->percentualAbsenteismo = $totalAgendados > 0
            ? round(($this->totalCancelados / $totalAgendados) * 100, 1)
            : 0.0;

        $this->dispatch(
            'chart-atualizar',
            produtividade: $this->serieProdutividade,
            ocupacao: $this->serieOcupacao,
            absenteismo: $this->serieAbsenteismo,
        );
    }

    private function aplicarFiltros(Builder $query): void
    {
        if ($this->profissionalId) {
            $query->where('user_id', $this->profissionalId);
        }

        if ($this->unidadeId) {
            $query->whereHas('sala', function ($q) {
                $q->where('unidade_id', $this->unidadeId);
            });
        }
    }

    protected function favoriteContext(): string
    {
        return 'relatorio_produtividade';
    }

    protected function favoritePayload(): array
    {
            return [
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
            'profissionalId' => $this->profissionalId,
            'unidadeId' => $this->unidadeId,
        ];
    }

    protected function applyFavoritePayload(array $payload): void
    {
        $this->dataInicio = $payload['dataInicio'] ?? $this->dataInicio;
        $this->dataFim = $payload['dataFim'] ?? $this->dataFim;
        $this->profissionalId = $payload['profissionalId'] ?? null;
        $this->unidadeId = $payload['unidadeId'] ?? null;

        $this->gerarRelatorio();
    }

    public function render()
    {
        $profissionais = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Profissional', 'Coordenador']);
        })->orderBy('name')->get();

        $unidades = Unidade::orderBy('nome')->get();

        return view('livewire.relatorio-produtividade', [
            'profissionais' => $profissionais,
            'unidades' => $unidades,
        ]);
    }
}
