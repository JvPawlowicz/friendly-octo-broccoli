<?php

namespace App\Listeners;

use App\Events\AtendimentoConcluido;
use App\Events\EvolucaoPendenteCriada;
use App\Mail\EvolucaoPendenteMail;
use App\Models\Evolucao;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CriarEvolucaoPendente implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 
     * Regra: Cria uma Evolução (status 'Rascunho') ligada ao paciente_id, 
     * user_id e atendimento_id do evento (Item 5.1)
     */
    public function handle(AtendimentoConcluido $event): void
    {
        $atendimento = $event->atendimento;

        // Verifica se já existe uma evolução para este atendimento
        $evolucaoExistente = Evolucao::where('atendimento_id', $atendimento->id)->first();

        if ($evolucaoExistente) {
            // Se já existe, não cria outra
            return;
        }

        // Cria uma nova evolução pendente
        $evolucao = Evolucao::create([
            'paciente_id' => $atendimento->paciente_id,
            'user_id' => $atendimento->user_id,
            'atendimento_id' => $atendimento->id,
            'status' => 'Rascunho',
            'relato_clinico' => null,
            'conduta' => null,
            'objetivos' => null,
            'evolucao_pai_id' => null,
            'finalizado_em' => null,
        ]);

        if ($evolucao->profissional && $evolucao->profissional->email) {
            Mail::to($evolucao->profissional->email)->send(new EvolucaoPendenteMail($evolucao));
        }

        // Dispara evento para broadcast (tempo real)
        event(new EvolucaoPendenteCriada($evolucao));
    }
}
