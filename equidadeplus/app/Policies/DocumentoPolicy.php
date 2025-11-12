<?php

namespace App\Policies;

use App\Models\Documento;
use App\Models\User;

class DocumentoPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('Admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Coordenador', 'Profissional', 'Secretaria']);
    }

    public function view(User $user, Documento $documento): bool
    {
        $unitId = $this->resolveUnitId($documento);

        if ($user->hasAnyRole(['Coordenador', 'Secretaria'])) {
            return $this->userHasUnit($user, $unitId);
        }

        if ($user->hasRole('Profissional')) {
            return $documento->user_id === $user->id
                || $this->userHasUnit($user, $unitId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Coordenador', 'Secretaria']);
    }

    public function delete(User $user, Documento $documento): bool
    {
        $unitId = $this->resolveUnitId($documento);

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

    protected function resolveUnitId(Documento $documento): ?int
    {
        if ($documento->relationLoaded('paciente')) {
            $paciente = $documento->getRelation('paciente');
        } else {
            $paciente = $documento->paciente;
        }

        return $paciente?->unidade_padrao_id;
    }
}


