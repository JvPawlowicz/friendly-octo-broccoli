<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Global Scope para filtrar registros por Unidade do usuário logado
 * 
 * Regra Master: Toda a lógica de acesso (ex: ver Pacientes, ver Agenda) 
 * para não-Admins deve ser filtrada com base na relação user->unidades()
 */
class UnidadeScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Se não há usuário autenticado, não aplica o scope
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Admins ignoram o filtro (Super Admin)
        if ($user->hasRole('Admin')) {
            return;
        }

        // Para outros usuários, filtra pelas unidades que eles pertencem
        $unidadeIds = $user->unidades()->pluck('unidades.id')->toArray();

        // Se o modelo tem relação direta com unidade (ex: pacientes.unidade_padrao_id)
        if ($model->getTable() === 'pacientes') {
            $builder->whereIn('unidade_padrao_id', $unidadeIds);
        }
        
        // Se o modelo tem relação através de outra tabela (ex: atendimentos -> salas -> unidades)
        // Isso será implementado nos modelos específicos que precisam
    }
}
