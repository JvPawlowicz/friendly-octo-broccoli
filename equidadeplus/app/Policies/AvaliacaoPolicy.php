<?php

namespace App\Policies;

use App\Models\Avaliacao;
use App\Models\User;

class AvaliacaoPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('Admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Coordenador', 'Profissional', 'Secretaria']);
    }

    public function view(User $user, Avaliacao $avaliacao): bool
    {
        $unitId = $this->resolveUnitId($avaliacao);

        if ($user->hasAnyRole(['Coordenador', 'Secretaria'])) {
            return $this->userHasUnit($user, $unitId);
        }

        if ($user->hasRole('Profissional')) {
            return $avaliacao->user_id === $user->id
                || $this->userHasUnit($user, $unitId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Profissional', 'Coordenador']);
    }

    public function update(User $user, Avaliacao $avaliacao): bool
    {
        return $avaliacao->user_id === $user->id && $avaliacao->status !== 'Finalizado';
    }

    public function delete(User $user, Avaliacao $avaliacao): bool
    {
        $unitId = $this->resolveUnitId($avaliacao);

        return $user->hasRole('Coordenador') && $this->userHasUnit($user, $unitId);
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

    protected function resolveUnitId(Avaliacao $avaliacao): ?int
    {
        if ($avaliacao->relationLoaded('paciente')) {
            $paciente = $avaliacao->getRelation('paciente');
        } else {
            $paciente = $avaliacao->paciente;
        }

        return $paciente?->unidade_padrao_id;
    }
}


