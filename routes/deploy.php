<?php

use App\Http\Controllers\DeployController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Deploy Routes
|--------------------------------------------------------------------------
|
| Rotas para deploy automático via webhook (GitHub, GitLab, etc.)
| Protegidas por token e rate limiting
|
*/

Route::post('/deploy', [DeployController::class, 'handle'])
    ->middleware(['throttle:10,1']) // Máximo 10 requisições por minuto
    ->name('deploy.webhook');

