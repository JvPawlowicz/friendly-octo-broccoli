<?php

namespace App\Providers;

use App\Events\AtendimentoConcluido;
use App\Events\AtendimentoAtualizado;
use App\Listeners\CriarEvolucaoPendente;
use App\Listeners\EnviarNotificacaoStatusAtendimento;
use App\Models\Atendimento;
use App\Models\Avaliacao;
use App\Models\Documento;
use App\Models\Evolucao;
use App\Models\Paciente;
use App\Policies\AtendimentoPolicy;
use App\Policies\AvaliacaoPolicy;
use App\Policies\DocumentoPolicy;
use App\Policies\EvolucaoPolicy;
use App\Policies\PacientePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Validar APP_KEY em produção (exceto quando executando key:generate)
        if (app()->environment('production') 
            && empty(config('app.key'))
            && !app()->runningInConsole()
            && !in_array('key:generate', $_SERVER['argv'] ?? [])) {
            throw new \RuntimeException(
                'APP_KEY não configurado! Execute: php artisan key:generate'
            );
        }

        // Registrar Eventos e Listeners
        Event::listen(
            AtendimentoConcluido::class,
            CriarEvolucaoPendente::class,
        );

        Event::listen(
            AtendimentoAtualizado::class,
            EnviarNotificacaoStatusAtendimento::class,
        );

        Gate::policy(Paciente::class, PacientePolicy::class);
        Gate::policy(Atendimento::class, AtendimentoPolicy::class);
        Gate::policy(Evolucao::class, EvolucaoPolicy::class);
        Gate::policy(Avaliacao::class, AvaliacaoPolicy::class);
        Gate::policy(Documento::class, DocumentoPolicy::class);

        // Registrar componente Blade para o layout do App
        Blade::component('components.layouts.app', 'app-layout');
    }
}
