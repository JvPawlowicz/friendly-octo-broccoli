<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UnidadeController extends Controller
{
    /**
     * Seleciona a unidade ativa para o usuário
     */
    public function selecionar(Request $request)
    {
        $unidadeId = $request->input('unidade_id');
        
        // Se for vazio, remove a seleção (mostra todas)
        if (empty($unidadeId)) {
            Session::forget('unidade_selecionada');
            return redirect()->back()->with('message', 'Filtro de unidade removido.');
        }
        
        $request->validate([
            'unidade_id' => 'required|exists:unidades,id',
        ]);
        
        // Verifica se o usuário pertence a esta unidade (ou é Admin)
        $user = Auth::user();
        if (!$user->hasRole('Admin')) {
            if (!$user->unidades->contains('id', $unidadeId)) {
                return redirect()->back()->with('error', 'Você não tem acesso a esta unidade.');
            }
        }

        // Salva na sessão
        Session::put('unidade_selecionada', $unidadeId);

        return redirect()->back()->with('message', 'Unidade selecionada com sucesso!');
    }
}

