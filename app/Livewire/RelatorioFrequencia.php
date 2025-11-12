<?php

namespace App\Livewire;

use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\User;
use App\Livewire\Concerns\HandlesFavorites;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class RelatorioFrequencia extends Component
{
    use HandlesFavorites;

    public $dataInicio;
    public $dataFim;
    public $profissionalId;
    public $pacienteId;
    public $favoriteSelecionado = null;
    
    public $dados = [];
    public $totalConcluidos = 0;
    public $totalCancelados = 0;

    public function mount()
    {
        // Verifica permissão - Admin tem acesso total
        if (!Auth::user()->can('ver_relatorios') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Você não tem permissão para acessar relatórios.');
        }
        
        // Define período padrão (últimos 30 dias)
        $this->dataInicio = now()->subDays(30)->format('Y-m-d');
        $this->dataFim = now()->format('Y-m-d');
        $this->gerarRelatorio();
        $this->carregarFavoritos();
    }
    
    public function exportar()
    {
        if (!Auth::user()->can('exportar_relatorios') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            session()->flash('error', 'Você não tem permissão para exportar relatórios.');
            return null;
        }

        $this->gerarRelatorio();
        $this->carregarFavoritos();
        $this->dispatch('app:toast', message: 'Relatório exportado.', type: 'info');

        $filename = 'relatorio_frequencia_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // cabeçalho
            fputcsv($handle, ['Paciente', 'CPF', 'Total', 'Concluídos', 'Cancelados', 'Taxa de Presença (%)'], ';');

            foreach ($this->dados as $item) {
                fputcsv($handle, [
                    $item['paciente']->nome_completo,
                    $item['paciente']->cpf ?? 'N/A',
                    $item['total'],
                    $item['concluidos'],
                    $item['cancelados'],
                    number_format($item['taxa_presenca'], 2, ',', '.'),
                ], ';');
            }

            fputcsv($handle, [] , ';');
            fputcsv($handle, ['TOTAL GERAL', '', '', $this->totalConcluidos, $this->totalCancelados, ''], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function gerarRelatorio()
    {
        $query = Atendimento::query()
            ->whereBetween('data_hora_inicio', [
                $this->dataInicio . ' 00:00:00',
                $this->dataFim . ' 23:59:59'
            ])
            ->with(['paciente', 'profissional']);

        // Filtros
        if ($this->profissionalId) {
            $query->where('user_id', $this->profissionalId);
        }

        if ($this->pacienteId) {
            $query->where('paciente_id', $this->pacienteId);
        }

        $atendimentos = $query->get();

        // Agrupa por paciente
        $this->dados = $atendimentos->groupBy('paciente_id')->map(function ($atendimentosPaciente) {
            $paciente = $atendimentosPaciente->first()->paciente;
            $concluidos = $atendimentosPaciente->where('status', 'Concluído')->count();
            $cancelados = $atendimentosPaciente->where('status', 'Cancelado')->count();
            $total = $atendimentosPaciente->count();

            return [
                'paciente' => $paciente,
                'total' => $total,
                'concluidos' => $concluidos,
                'cancelados' => $cancelados,
                'taxa_presenca' => $total > 0 ? round(($concluidos / $total) * 100, 2) : 0,
            ];
        })->values();

        $this->totalConcluidos = $atendimentos->where('status', 'Concluído')->count();
        $this->totalCancelados = $atendimentos->where('status', 'Cancelado')->count();
    }

    public function render()
    {
        $profissionais = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Profissional', 'Coordenador']);
        })->orderBy('name')->get();

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

        return view('livewire.relatorio-frequencia', [
            'profissionais' => $profissionais,
            'pacientes' => $pacientes,
        ]);
    }

    protected function favoriteContext(): string
    {
        return 'relatorio_frequencia';
    }

    protected function favoritePayload(): array
    {
        return [
            'dataInicio' => $this->dataInicio,
            'dataFim' => $this->dataFim,
            'profissionalId' => $this->profissionalId,
            'pacienteId' => $this->pacienteId,
        ];
    }

    protected function applyFavoritePayload(array $payload): void
    {
        $this->dataInicio = $payload['dataInicio'] ?? $this->dataInicio;
        $this->dataFim = $payload['dataFim'] ?? $this->dataFim;
        $this->profissionalId = $payload['profissionalId'] ?? null;
        $this->pacienteId = $payload['pacienteId'] ?? null;

        $this->gerarRelatorio();
    }
}
