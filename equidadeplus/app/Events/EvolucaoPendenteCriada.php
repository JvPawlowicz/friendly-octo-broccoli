<?php

namespace App\Events;

use App\Models\Evolucao;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EvolucaoPendenteCriada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $evolucao;

    /**
     * Create a new event instance.
     */
    public function __construct(Evolucao $evolucao)
    {
        $this->evolucao = $evolucao;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast para o profissional específico
        return [
            new PrivateChannel('user.' . $this->evolucao->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'EvolucaoPendenteCriada';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'evolucao_id' => $this->evolucao->id,
            'paciente_id' => $this->evolucao->paciente_id,
            'paciente_nome' => $this->evolucao->paciente->nome_completo,
            'atendimento_id' => $this->evolucao->atendimento_id,
            'created_at' => $this->evolucao->created_at->toIso8601String(),
        ];
    }
}
