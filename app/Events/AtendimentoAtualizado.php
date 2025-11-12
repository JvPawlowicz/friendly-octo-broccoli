<?php

namespace App\Events;

use App\Models\Atendimento;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AtendimentoAtualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $atendimento;

    /**
     * Create a new event instance.
     */
    public function __construct(Atendimento $atendimento)
    {
        $this->atendimento = $atendimento;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast para o canal da unidade do atendimento
        $unidadeId = $this->atendimento->sala?->unidade_id ?? 
                     $this->atendimento->paciente->unidade_padrao_id;
        
        return [
            new PrivateChannel('agenda.' . $unidadeId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'AtendimentoAtualizado';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'atendimento_id' => $this->atendimento->id,
            'status' => $this->atendimento->status,
            'paciente_id' => $this->atendimento->paciente_id,
            'user_id' => $this->atendimento->user_id,
            'data_hora_inicio' => $this->atendimento->data_hora_inicio->toIso8601String(),
            'data_hora_fim' => $this->atendimento->data_hora_fim->toIso8601String(),
        ];
    }
}
