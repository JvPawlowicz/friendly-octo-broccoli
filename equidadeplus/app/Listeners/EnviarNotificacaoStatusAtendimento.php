<?php

namespace App\Listeners;

use App\Events\AtendimentoAtualizado;
use App\Mail\AtendimentoStatusMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacaoStatusAtendimento implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(AtendimentoAtualizado $event): void
    {
        $status = $event->atendimento->status;
        if (!in_array($status, ['Confirmado', 'Cancelado'])) {
            return;
        }

        $atendimento = $event->atendimento->fresh(['paciente', 'profissional', 'sala.unidade']);
        if (!$atendimento) {
            return;
        }

        $destinatarios = collect([
            $atendimento->paciente?->email_principal,
            $atendimento->profissional?->email,
        ])->filter()->unique();

        if ($destinatarios->isEmpty()) {
            return;
        }

        foreach ($destinatarios as $email) {
            Mail::to($email)->send(new AtendimentoStatusMail($atendimento));
        }
    }
}


