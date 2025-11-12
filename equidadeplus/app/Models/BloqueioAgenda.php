<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloqueioAgenda extends Model
{
    use HasFactory;

    protected $table = 'bloqueio_agendas';

    protected $fillable = [
        'titulo_bloqueio',
        'data_hora_inicio',
        'data_hora_fim',
        'unidade_id',
        'user_id',
        'sala_id',
    ];

    protected $casts = [
        'data_hora_inicio' => 'datetime',
        'data_hora_fim' => 'datetime',
    ];

    public function unidade(): BelongsTo {
        return $this->belongsTo(Unidade::class);
    }

    public function profissional(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sala(): BelongsTo {
        return $this->belongsTo(Sala::class);
    }
}
