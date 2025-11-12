<?php

namespace App\Mail;

use App\Models\Atendimento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AtendimentoStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Atendimento $atendimento;

    /**
     * Create a new message instance.
     */
    public function __construct(Atendimento $atendimento)
    {
        $this->atendimento = $atendimento->loadMissing(['paciente', 'profissional', 'sala.unidade']);
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $status = $this->atendimento->status;
        $titulo = sprintf('Atualização do atendimento - %s', $status);

        return $this->subject($titulo)
            ->view('emails.atendimentos.status', [
                'atendimento' => $this->atendimento,
                'status' => $status,
            ]);
    }
}


