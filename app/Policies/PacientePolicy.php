<?php

namespace App\Policies;

use App\Models\Paciente;
use App\Models\User;

class PacientePolicy
{
    /**
     * Administradores têm acesso total.
     */
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('Admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Coordenador', 'Profissional', 'Secretaria']);
    }

    public function view(User $user, Paciente $paciente): bool
    {
        if ($user->hasAnyRole(['Coordenador', 'Secretaria'])) {
            return $this->userHasUnit($user, $paciente->unidade_padrao_id);
        }

        if ($user->hasRole('Profissional')) {
            if ($this->userHasUnit($user, $paciente->unidade_padrao_id)) {
                return true;
            }

            return $paciente->atendimentos()
                    ->where('user_id', $user->id)
                    ->exists()
                || $paciente->evolucoes()
                    ->where('user_id', $user->id)
                    ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Coordenador', 'Secretaria']);
    }

    public function update(User $user, Paciente $paciente): bool
    {
        if ($user->hasAnyRole(['Coordenador', 'Secretaria'])) {
            return $this->userHasUnit($user, $paciente->unidade_padrao_id);
        }

        if ($user->hasRole('Profissional')) {
            return $paciente->atendimentos()
                ->where('user_id', $user->id)
                ->exists();
        }

        return false;
    }

    public function delete(User $user, Paciente $paciente): bool
    {
        return $user->hasAnyRole(['Coordenador']);
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
}


