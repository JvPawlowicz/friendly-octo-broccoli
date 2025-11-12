<?php

namespace App\Mail;

use App\Models\Evolucao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EvolucaoPendenteMail extends Mailable
{
    use Queueable, SerializesModels;

    public Evolucao $evolucao;

    /**
     * Create a new message instance.
     */
    public function __construct(Evolucao $evolucao)
    {
        $this->evolucao = $evolucao->loadMissing(['paciente', 'profissional', 'atendimento']);
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $paciente = $this->evolucao->paciente?->nome_completo ?? 'Paciente';
        $titulo = sprintf('Evolução pendente - %s', $paciente);

        return $this->subject($titulo)
            ->view('emails.evolucoes.pendente', [
                'evolucao' => $this->evolucao,
            ]);
    }
}


