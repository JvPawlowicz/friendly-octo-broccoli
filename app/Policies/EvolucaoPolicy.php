<?php

namespace App\Policies;

use App\Models\Evolucao;
use App\Models\User;

class EvolucaoPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('Admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Coordenador', 'Profissional', 'Secretaria']);
    }

    public function view(User $user, Evolucao $evolucao): bool
    {
        $unitId = $this->resolveUnitId($evolucao);

        if ($user->hasAnyRole(['Coordenador', 'Secretaria'])) {
            return $this->userHasUnit($user, $unitId);
        }

        if ($user->hasRole('Profissional')) {
            return $evolucao->user_id === $user->id
                || $this->userHasUnit($user, $unitId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Profissional');
    }

    public function update(User $user, Evolucao $evolucao): bool
    {
        if ($evolucao->status === 'Finalizado') {
            return false;
        }

        return $evolucao->user_id === $user->id;
    }

    public function addAddendum(User $user, Evolucao $evolucao): bool
    {
        return $evolucao->status === 'Finalizado'
            && $evolucao->user_id === $user->id;
    }

    protected function userHasUnit(User $user, ?int $unitId): bool
    {
        if (!$unitId) {
            return false;
        }

        return $user->unidades()
            ->where('unidades.id', $unitId)
            ->exists();
    }

    protected function resolveUnitId(Evolucao $evolucao): ?int
    {
        if ($evolucao->relationLoaded('atendimento')) {
            $atendimento = $evolucao->getRelation('atendimento');
        } else {
            $atendimento = $evolucao->atendimento;
        }

        if ($atendimento) {
            if ($atendimento->relationLoaded('sala')) {
                $sala = $atendimento->getRelation('sala');
            } else {
                $sala = $atendimento->sala;
            }

            if ($sala && $sala->unidade_id) {
                return $sala->unidade_id;
            }
        }

        if ($evolucao->relationLoaded('paciente')) {
            $paciente = $evolucao->getRelation('paciente');
        } else {
            $paciente = $evolucao->paciente;
        }

        return $paciente?->unidade_padrao_id;
    }
}


