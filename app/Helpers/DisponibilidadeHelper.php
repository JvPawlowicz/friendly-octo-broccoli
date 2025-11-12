<?php

namespace App\Helpers;

use App\Models\DisponibilidadeUsuario;
use App\Models\User;
use Carbon\Carbon;

class DisponibilidadeHelper
{
    /**
     * Verifica se um profissional está disponível em um determinado horário
     */
    public static function verificarDisponibilidade(int $userId, Carbon $dataHoraInicio, Carbon $dataHoraFim): bool
    {
        $diaSemana = $dataHoraInicio->dayOfWeek; // 0 = Domingo, 6 = Sábado
        $horaInicio = $dataHoraInicio->format('H:i:s');
        $horaFim = $dataHoraFim->format('H:i:s');

        // Busca disponibilidades do profissional para o dia da semana
        $disponibilidades = DisponibilidadeUsuario::where('user_id', $userId)
            ->where('dia_da_semana', $diaSemana)
            ->get();

        if ($disponibilidades->isEmpty()) {
            return false; // Sem disponibilidade cadastrada para este dia
        }

        // Verifica se o horário solicitado está dentro de alguma disponibilidade
        foreach ($disponibilidades as $disponibilidade) {
            if ($horaInicio >= $disponibilidade->hora_inicio && 
                $horaFim <= $disponibilidade->hora_fim) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna os horários disponíveis de um profissional em uma data específica
     */
    public static function obterHorariosDisponiveis(int $userId, Carbon $data, int $duracaoMinutos = 60): array
    {
        $diaSemana = $data->dayOfWeek;
        $disponibilidades = DisponibilidadeUsuario::where('user_id', $userId)
            ->where('dia_da_semana', $diaSemana)
            ->get();

        $horarios = [];

        foreach ($disponibilidades as $disponibilidade) {
            $inicio = Carbon::parse($data->format('Y-m-d') . ' ' . $disponibilidade->hora_inicio);
            $fim = Carbon::parse($data->format('Y-m-d') . ' ' . $disponibilidade->hora_fim);

            // Gera slots de horários baseado na duração
            $atual = $inicio->copy();
            while ($atual->copy()->addMinutes($duracaoMinutos)->lte($fim)) {
                $horarios[] = [
                    'inicio' => $atual->format('H:i'),
                    'fim' => $atual->copy()->addMinutes($duracaoMinutos)->format('H:i'),
                ];
                $atual->addMinutes($duracaoMinutos);
            }
        }

        return $horarios;
    }

    /**
     * Verifica se um profissional está ativo
     */
    public static function profissionalAtivo(int $userId): bool
    {
        $user = User::find($userId);
        return $user && $user->status === true;
    }
}

