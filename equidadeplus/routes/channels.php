<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado para usuário específico (Fluxo 2.3)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Canal privado para agenda da unidade (Fluxo 1.4)
Broadcast::channel('agenda.{unidadeId}', function ($user, $unidadeId) {
    // Usuário pode ver se tem acesso à unidade ou é Admin
    if ($user->hasRole('Admin')) {
        return true;
    }
    return $user->unidades->contains('id', $unidadeId);
});
