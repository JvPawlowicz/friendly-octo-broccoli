<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'assunto',
        'mensagem',
        'status',
        'resposta',
        'respondido_por',
        'respondido_em',
    ];

    protected $casts = [
        'respondido_em' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respondidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondido_por');
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'warning',
            'em_andamento' => 'info',
            'resolvido' => 'success',
            'fechado' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'em_andamento' => 'Em Andamento',
            'resolvido' => 'Resolvido',
            'fechado' => 'Fechado',
            default => 'Desconhecido',
        };
    }
}
