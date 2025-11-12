<?php

namespace App\Policies;

use App\Models\Atendimento;
use App\Models\User;

class AtendimentoPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('Admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Coordenador', 'Profissional', 'Secretaria']);
    }

    public function view(User $user, Atendimento $atendimento): bool
    {
        $unitId = $this->resolveUnitId($atendimento);

        if ($user->hasAnyRole(['Coordenador', 'Secretaria'])) {
            return $this->userHasUnit($user, $unitId);
        }

        if ($user->hasRole('Profissional')) {
            return $atendimento->user_id === $user->id
                || $this->userHasUnit($user, $unitId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Coordenador', 'Secretaria']);
    }

    public function update(User $user, Atendimento $atendimento): bool
    {
        $unitId = $this->resolveUnitId($atendimento);

        if ($user->hasAnyRole(['Coordenador', 'Secretaria'])) {
            return $this->userHasUnit($user, $unitId);
        }

        if ($user->hasRole('Profissional')) {
            return $atendimento->user_id === $user->id && $atendimento->status !== 'Finalizado';
        }

        return false;
    }

    public function delete(User $user, Atendimento $atendimento): bool
    {
        $unitId = $this->resolveUnitId($atendimento);

        return $user->hasRole('Coordenador') && $this->userHasUnit($user, $unitId);
    }

    public function concluir(User $user, Atendimento $atendimento): bool
    {
        return $user->hasRole('Profissional') && $atendimento->user_id === $user->id;
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

    protected function resolveUnitId(Atendimento $atendimento): ?int
    {
        if ($atendimento->relationLoaded('sala')) {
            $sala = $atendimento->getRelation('sala');
        } else {
            $sala = $atendimento->sala;
        }

        if ($sala && $sala->unidade_id) {
            return $sala->unidade_id;
        }

        if ($atendimento->relationLoaded('paciente')) {
            $paciente = $atendimento->getRelation('paciente');
        } else {
            $paciente = $atendimento->paciente;
        }

        return $paciente?->unidade_padrao_id;
    }
}


