<?php

namespace App\Livewire;

use App\Livewire\Concerns\HandlesFavorites;
use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Unidade;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('components.layouts.app')]
class Relatorios extends Component
{
    use HandlesFavorites;

    // Tipo de relatório ativo
    public string $tipoRelatorio = 'produtividade'; // 'produtividade' | 'frequencia'

    // Filtros compartilhados
    public string $dataInicio;
    public string $dataFim;
    public ?int $profissionalId = null;
    public ?int $unidadeId = null;
    public ?int $pacienteId = null;
    public $favoriteSelecionado = null;

    // Dados de Produtividade
    public array $dadosProdutividade = [];
    public int $totalAtendimentos = 0;
    public int $totalCancelados = 0;
    public array $serieProdutividade = ['labels' => [], 'totais' => [], 'medias' => []];
    public array $serieOcupacao = ['labels' => [], 'valores' => []];
    public array $serieAbsenteismo = ['labels' => ['Realizados', 'Cancelados'], 'valores' => [0, 0]];
    public float $percentualAbsenteismo = 0.0;

    // Dados de Frequência
    public array $dadosFrequencia = [];
    public int $totalConcluidos = 0;
    public int $totalCanceladosFreq = 0;

    public function mount()
    {
        if (!Auth::user()->can('ver_relatorios') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para acessar relatórios.');
        }

        $this->dataInicio = now()->subDays(30)->format('Y-m-d');
        $this->dataFim = now()->format('Y-m-d');
        $this->carregarDados();
        $this->carregarFavoritos();
    }

    public function updatedTipoRelatorio()
    {
        $this->carregarDados();
    }

    public function updatedDataInicio()
    {
        $this->carregarDados();
    }

    public function updatedDataFim()
    {
        $this->carregarDados();
    }

    public function updatedProfissionalId()
    {
        $this->carregarDados();
    }

    public function updatedUnidadeId()
    {
        $this->carregarDados();
    }

    public function updatedPacienteId()
    {
        $this->carregarDados();
    }

    public function carregarDados()
    {
        if ($this->tipoRelatorio === 'produtividade') {
            $this->gerarRelatorioProdutividade();
        } else {
            $this->gerarRelatorioFrequencia();
        }
    }

    protected function gerarRelatorioProdutividade()
    {
        $query = Atendimento::query()
            ->contaveis() // Filtra apenas atendimentos contáveis
            ->whereBetween('data_hora_inicio', [
                $this->dataInicio . ' 00:00:00',
                $this->dataFim . ' 23:59:59'
            ])
            ->where('status', 'Concluído')
            ->with(['profissional']);

        $this->aplicarFiltros($query);

        $atendimentos = $query->get();

        // Agrupar por profissional
        $porProfissional = $atendimentos->groupBy('user_id');

        $this->dadosProdutividade = [];
        $this->totalAtendimentos = $atendimentos->count();
        $this->totalCancelados = Atendimento::contaveis()
            ->whereBetween('data_hora_inicio', [
                $this->dataInicio . ' 00:00:00',
                $this->dataFim . ' 23:59:59'
            ])->where('status', 'Cancelado')->count();

        foreach ($porProfissional as $userId => $atendimentosProf) {
            $profissional = $atendimentosProf->first()->profissional;
            $diasTrabalhados = $atendimentosProf->groupBy(function ($item) {
                return $item->data_hora_inicio->format('Y-m-d');
            })->count();

            $this->dadosProdutividade[] = [
                'profissional_id' => $userId,
                'profissional_nome' => $profissional->name ?? 'N/A',
                'total' => $atendimentosProf->count(),
                'dias_trabalhados' => $diasTrabalhados,
                'media_diaria' => $diasTrabalhados > 0 ? $atendimentosProf->count() / $diasTrabalhados : 0,
            ];
        }

        // Ordenar por total
        usort($this->dadosProdutividade, fn($a, $b) => $b['total'] <=> $a['total']);

        // Séries para gráficos
        $this->serieProdutividade = [
            'labels' => array_column($this->dadosProdutividade, 'profissional_nome'),
            'totais' => array_column($this->dadosProdutividade, 'total'),
            'medias' => array_column($this->dadosProdutividade, 'media_diaria'),
        ];

        $this->serieAbsenteismo = [
            'labels' => ['Realizados', 'Cancelados'],
            'valores' => [$this->totalAtendimentos, $this->totalCancelados],
        ];

        $total = $this->totalAtendimentos + $this->totalCancelados;
        $this->percentualAbsenteismo = $total > 0 ? ($this->totalCancelados / $total) * 100 : 0;
    }

    protected function gerarRelatorioFrequencia()
    {
        $query = Atendimento::query()
            ->contaveis() // Filtra apenas atendimentos contáveis
            ->whereBetween('data_hora_inicio', [
                $this->dataInicio . ' 00:00:00',
                $this->dataFim . ' 23:59:59'
            ])
            ->with(['paciente', 'profissional']);

        $this->aplicarFiltros($query);

        $atendimentos = $query->get();

        // Agrupar por paciente
        $porPaciente = $atendimentos->groupBy('paciente_id');

        $this->dadosFrequencia = [];
        $this->totalConcluidos = 0;
        $this->totalCanceladosFreq = 0;

        foreach ($porPaciente as $pacienteId => $atendimentosPac) {
            $paciente = $atendimentosPac->first()->paciente;
            $concluidos = $atendimentosPac->where('status', 'Concluído')->count();
            $cancelados = $atendimentosPac->where('status', 'Cancelado')->count();
            $total = $atendimentosPac->count();
            $taxaPresenca = $total > 0 ? ($concluidos / $total) * 100 : 0;

            $this->dadosFrequencia[] = [
                'paciente_id' => $pacienteId,
                'paciente' => $paciente,
                'total' => $total,
                'concluidos' => $concluidos,
                'cancelados' => $cancelados,
                'taxa_presenca' => $taxaPresenca,
            ];

            $this->totalConcluidos += $concluidos;
            $this->totalCanceladosFreq += $cancelados;
        }

        // Ordenar por taxa de presença
        usort($this->dadosFrequencia, fn($a, $b) => $b['taxa_presenca'] <=> $a['taxa_presenca']);
    }

    protected function aplicarFiltros($query)
    {
        $user = Auth::user();

        if ($this->profissionalId) {
            $query->where('user_id', $this->profissionalId);
        }

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

        if ($this->pacienteId) {
            $query->where('paciente_id', $this->pacienteId);
        }
    }

    public function exportar()
    {
        if (!Auth::user()->can('exportar_relatorios') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            $this->dispatch('app:toast', message: 'Você não tem permissão para exportar relatórios.', type: 'error');
            return;
        }

        $this->carregarDados();

        if ($this->tipoRelatorio === 'produtividade') {
            return $this->exportarProdutividade();
        } else {
            return $this->exportarFrequencia();
        }
    }

    public function exportarPDF()
    {
        if (!Auth::user()->can('exportar_relatorios') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            $this->dispatch('app:toast', message: 'Você não tem permissão para exportar relatórios.', type: 'error');
            return;
        }

        $this->carregarDados();

        $data = [
            'tipoRelatorio' => $this->tipoRelatorio,
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
            'usuario' => Auth::user(),
            'geradoEm' => now()->format('d/m/Y H:i:s'),
        ];

        if ($this->tipoRelatorio === 'produtividade') {
            $data['dadosProdutividade'] = $this->dadosProdutividade;
            $data['totalAtendimentos'] = $this->totalAtendimentos;
            $data['totalCancelados'] = $this->totalCancelados;
            $data['percentualAbsenteismo'] = $this->percentualAbsenteismo;
            $data['profissionalSelecionado'] = $this->profissionalId 
                ? User::find($this->profissionalId)?->name 
                : null;
            $data['unidadeSelecionada'] = $this->unidadeId 
                ? Unidade::find($this->unidadeId)?->nome 
                : null;
            
            $view = 'pdf.relatorio-produtividade';
            $filename = 'relatorio_produtividade_' . now()->format('Ymd_His') . '.pdf';
        } else {
            $data['dadosFrequencia'] = $this->dadosFrequencia;
            $data['totalConcluidos'] = $this->totalConcluidos;
            $data['totalCanceladosFreq'] = $this->totalCanceladosFreq;
            $data['pacienteSelecionado'] = $this->pacienteId 
                ? Paciente::find($this->pacienteId)?->nome_completo 
                : null;
            $data['unidadeSelecionada'] = $this->unidadeId 
                ? Unidade::find($this->unidadeId)?->nome 
                : null;
            
            $view = 'pdf.relatorio-frequencia';
            $filename = 'relatorio_frequencia_' . now()->format('Ymd_His') . '.pdf';
        }

        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('a4', 'landscape');
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    protected function exportarProdutividade()
    {
        $filename = 'relatorio_produtividade_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Profissional', 'Total de Atendimentos', 'Dias Trabalhados', 'Média Diária'], ';');

            foreach ($this->dadosProdutividade as $item) {
                fputcsv($handle, [
                    $item['profissional_nome'],
                    $item['total'],
                    $item['dias_trabalhados'],
                    number_format($item['media_diaria'], 2, ',', '.'),
                ], ';');
            }

            fputcsv($handle, [], ';');
            fputcsv($handle, ['TOTAL CONCLUÍDOS', $this->totalAtendimentos, '', ''], ';');
            fputcsv($handle, ['TOTAL CANCELADOS', $this->totalCancelados, '', ''], ';');
            fputcsv($handle, ['Percentual de absenteísmo', number_format($this->percentualAbsenteismo, 2, ',', '.') . '%', '', ''], ';');

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportarFrequencia()
    {
        $filename = 'relatorio_frequencia_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Paciente', 'CPF', 'Total', 'Concluídos', 'Cancelados', 'Taxa de Presença (%)'], ';');

            foreach ($this->dadosFrequencia as $item) {
                fputcsv($handle, [
                    $item['paciente']->nome_completo,
                    $item['paciente']->cpf ?? 'N/A',
                    $item['total'],
                    $item['concluidos'],
                    $item['cancelados'],
                    number_format($item['taxa_presenca'], 2, ',', '.'),
                ], ';');
            }

            fputcsv($handle, [], ';');
            fputcsv($handle, ['TOTAL GERAL', '', '', $this->totalConcluidos, $this->totalCanceladosFreq, ''], ';');

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function favoriteContext(): string
    {
        return 'relatorios_' . $this->tipoRelatorio;
    }

    protected function favoritePayload(): array
    {
        return [
            'tipo_relatorio' => $this->tipoRelatorio,
            'data_inicio' => $this->dataInicio,
            'data_fim' => $this->dataFim,
            'profissional_id' => $this->profissionalId,
            'unidade_id' => $this->unidadeId,
            'paciente_id' => $this->pacienteId,
        ];
    }

    protected function applyFavoritePayload(array $payload): void
    {
        $this->tipoRelatorio = $payload['tipo_relatorio'] ?? 'produtividade';
        $this->dataInicio = $payload['data_inicio'] ?? now()->subDays(30)->format('Y-m-d');
        $this->dataFim = $payload['data_fim'] ?? now()->format('Y-m-d');
        $this->profissionalId = $payload['profissional_id'] ?? null;
        $this->unidadeId = $payload['unidade_id'] ?? null;
        $this->pacienteId = $payload['paciente_id'] ?? null;
        $this->carregarDados();
    }

    public function render()
    {
        $unidades = Auth::user()->hasRole('Admin')
            ? Unidade::orderBy('nome')->get()
            : Auth::user()->unidades()->orderBy('nome')->get();

        $profissionais = User::whereHas('roles', function ($q) {
            $q->where('name', 'Profissional');
        })->orderBy('name')->get();

        $pacientes = Paciente::query();
        if (!$unidades->isEmpty() && !Auth::user()->hasRole('Admin')) {
            $unidadeIds = $unidades->pluck('id')->toArray();
            $pacientes->whereIn('unidade_padrao_id', $unidadeIds);
        }
        $pacientes = $pacientes->orderBy('nome_completo')->get();

        return view('livewire.relatorios', [
            'unidades' => $unidades,
            'profissionais' => $profissionais,
            'pacientes' => $pacientes,
        ]);
    }
}

