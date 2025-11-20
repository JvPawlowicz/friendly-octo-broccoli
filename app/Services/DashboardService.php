<?php

namespace App\Services;

use App\Models\Atendimento;
use App\Models\Avaliacao;
use App\Models\Evolucao;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class DashboardService
{
    protected static ?bool $avaliacaoRespostasTemValorNumeric = null;

    public function metricsFor(User $user, ?int $unidadeSelecionada = null): array
    {
        $rolesKey = $user->roles->pluck('name')->sort()->implode('-') ?: 'sem-role';
        $unidadeKey = $unidadeSelecionada ?: 'todas';
        $cacheKey = sprintf('dashboard.metrics.%s.%s.%s', $user->id, $unidadeKey, $rolesKey);

        return Cache::remember($cacheKey, now()->addSeconds(60), function () use ($user, $unidadeSelecionada) {
            $escopo = $this->construirEscopo($user, $unidadeSelecionada);
            $hoje = Carbon::today();

            $atendimentosBase = Atendimento::query()
                ->contaveis() // Filtra apenas atendimentos contáveis
                ->when($escopo['user_id'], fn ($q, $id) => $q->where('user_id', $id))
                ->when($escopo['unidade_ids'], function ($q) use ($escopo) {
                    $q->whereHas('sala', fn ($sub) => $sub->whereIn('unidade_id', $escopo['unidade_ids']));
                });

            $atendimentosHoje = (clone $atendimentosBase)
                ->whereDate('data_hora_inicio', $hoje)
                ->whereIn('status', ['Agendado', 'Confirmado', 'Check-in'])
                ->count();

            $pendenciasConfirmacao = (clone $atendimentosBase)
                ->whereIn('status', ['Agendado'])
                ->count();

            $canceladosHoje = (clone $atendimentosBase)
                ->where('status', 'Cancelado')
                ->whereDate('updated_at', $hoje)
                ->count();

            $evolucoesBase = Evolucao::query()
                ->where('status', 'Rascunho')
                ->when($escopo['user_id'], fn ($q, $id) => $q->where('user_id', $id))
                ->when(!$escopo['user_id'] && $escopo['unidade_ids'], function ($q) use ($escopo) {
                    $q->whereHas('paciente', fn ($sub) => $sub->whereIn('unidade_padrao_id', $escopo['unidade_ids']));
                });

            $evolucoesPendentes = (clone $evolucoesBase)->count();

            $avaliacoesBase = Avaliacao::query()
                ->where('status', '!=', 'Finalizado')
                ->when($escopo['user_id'], fn ($q, $id) => $q->where('user_id', $id))
                ->when(!$escopo['user_id'] && $escopo['unidade_ids'], function ($q) use ($escopo) {
                    $q->whereHas('paciente', fn ($sub) => $sub->whereIn('unidade_padrao_id', $escopo['unidade_ids']));
                });

            $avaliacoesAbertas = (clone $avaliacoesBase)->count();

            $pacientesBase = Paciente::query()
                ->when($escopo['unidade_ids'], fn ($q, $ids) => $q->whereIn('unidade_padrao_id', $ids));

            $pacientesAtivos = (clone $pacientesBase)->where('status', 'Ativo')->count();
            $novosPacientesMes = (clone $pacientesBase)
                ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()])
                ->count();

            $profissionaisAtivos = User::role('Profissional')
                ->when(Schema::hasColumn('users', 'status'), fn ($q) => $q->where('status', true))
                ->when($escopo['unidade_ids'], function ($q) use ($escopo) {
                    $q->whereHas('unidades', fn ($sub) => $sub->whereIn('unidades.id', $escopo['unidade_ids']));
                })
                ->count();

            $cards = [
                [
                    'label' => 'Consultas hoje',
                    'value' => $this->formatMetric($atendimentosHoje),
                    'description' => $escopo['user_id']
                        ? 'Agenda do seu dia'
                        : ($escopo['unidade_ids'] ? 'Agenda da unidade selecionada' : 'Agenda geral'),
                ],
                [
                    'label' => 'Evoluções pendentes',
                    'value' => $this->formatMetric($evolucoesPendentes),
                    'description' => $escopo['user_id']
                        ? 'Rascunhos aguardando finalização'
                        : 'Pendências da equipe',
                ],
            ];

            if ($escopo['user_id'] && $user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
                $cards[] = [
                    'label' => 'Avaliações abertas',
                    'value' => $this->formatMetric($avaliacoesAbertas),
                    'description' => 'Avaliações em andamento',
                ];

                $satisfacaoMedia = $this->calcularSatisfacaoMedia($user->id, $escopo['unidade_ids']);
                $cards[] = [
                    'label' => 'Satisfação geral',
                    'value' => $satisfacaoMedia ?? '—',
                    'description' => 'Últimos 30 dias',
                ];
            }

            if ($user->hasRole('Secretaria') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
                $cards[] = [
                    'label' => 'Agendamentos pendentes',
                    'value' => $this->formatMetric($pendenciasConfirmacao),
                    'description' => 'Aguardando confirmação',
                ];
                $cards[] = [
                    'label' => 'Cancelados hoje',
                    'value' => $this->formatMetric($canceladosHoje),
                    'description' => 'Atualizados hoje',
                ];
                $cards[] = [
                    'label' => 'Novos pacientes (mês)',
                    'value' => $this->formatMetric($novosPacientesMes),
                    'description' => 'Cadastros no mês atual',
                ];
            }

            if ($user->hasAnyRole(['Admin', 'Coordenador'])) {
                $cards[] = [
                    'label' => 'Pacientes ativos',
                    'value' => $this->formatMetric($pacientesAtivos),
                    'description' => $escopo['unidade_ids'] ? 'Unidades selecionadas' : 'Todas as unidades',
                ];
                $cards[] = [
                    'label' => 'Profissionais ativos',
                    'value' => $this->formatMetric($profissionaisAtivos),
                    'description' => 'Com acesso ao sistema',
                ];
                $cards[] = [
                    'label' => 'Novos pacientes (mês)',
                    'value' => $this->formatMetric($novosPacientesMes),
                    'description' => 'Cadastros no mês atual',
                ];
            }

            return ['cards' => $cards];
        });
    }

    protected function construirEscopo(User $user, ?int $unidadeSelecionada): array
    {
        $escopo = [
            'user_id' => null,
            'unidade_ids' => null,
        ];

        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $escopo['user_id'] = $user->id;
        } else {
            if ($unidadeSelecionada) {
                $escopo['unidade_ids'] = [$unidadeSelecionada];
            } elseif (!$user->hasRole('Admin')) {
                $escopo['unidade_ids'] = $user->unidades()->pluck('unidades.id')->toArray();
            }
        }

        return $escopo;
    }

    protected function calcularSatisfacaoMedia(int $userId, ?array $unidadeIds): ?string
    {
        if (!$this->avaliacaoRespostasTemValorNumeric()) {
            return null;
        }

        $query = \Illuminate\Support\Facades\DB::table('avaliacao_respostas')
            ->join('avaliacaos', 'avaliacao_respostas.avaliacao_id', '=', 'avaliacaos.id')
            ->where('avaliacaos.user_id', $userId)
            ->whereBetween('avaliacao_respostas.created_at', [now()->subDays(30), now()]);

        if ($unidadeIds) {
            $query->whereExists(function ($sub) use ($unidadeIds) {
                $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('pacientes')
                    ->whereColumn('pacientes.id', 'avaliacaos.paciente_id')
                    ->whereIn('pacientes.unidade_padrao_id', $unidadeIds);
            });
        }

        $media = $query->avg('avaliacao_respostas.valor_numeric');

        return $media ? number_format($media, 1, ',', '.') : null;
    }

    protected function formatMetric(int|float $valor): string
    {
        return number_format($valor, 0, ',', '.');
    }

    protected function avaliacaoRespostasTemValorNumeric(): bool
    {
        if (self::$avaliacaoRespostasTemValorNumeric !== null) {
            return self::$avaliacaoRespostasTemValorNumeric;
        }

        return self::$avaliacaoRespostasTemValorNumeric = Schema::hasColumn('avaliacao_respostas', 'valor_numeric');
    }
}


