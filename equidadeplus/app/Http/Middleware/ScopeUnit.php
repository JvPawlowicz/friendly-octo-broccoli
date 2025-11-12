<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ScopeUnit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Admin pode acessar todas as unidades – apenas valida se a unidade selecionada é válida
        if ($user->hasRole('Admin')) {
            $unidadeSelecionada = Session::get('unidade_selecionada');

            if ($unidadeSelecionada) {
                $possuiUnidade = $user->unidades()
                    ->where('unidades.id', $unidadeSelecionada)
                    ->exists();

                if (!$possuiUnidade) {
                    $existe = \App\Models\Unidade::where('id', $unidadeSelecionada)->exists();

                    if (!$existe) {
                        Session::forget('unidade_selecionada');
                    }
                }
            }

            return $next($request);
        }

        $unidadesUsuario = $user->unidades()->pluck('unidades.id');

        if ($unidadesUsuario->isEmpty()) {
            Session::forget('unidade_selecionada');

            return $next($request);
        }

        $selecionada = Session::get('unidade_selecionada');

        if (!$selecionada || !$unidadesUsuario->contains($selecionada)) {
            Session::put('unidade_selecionada', $unidadesUsuario->first());
        }

        return $next($request);
    }
}


