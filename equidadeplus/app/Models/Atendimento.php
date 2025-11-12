<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Atendimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'user_id',
        'sala_id',
        'status',
        'data_hora_inicio',
        'data_hora_fim',
        'recorrencia_id',
    ];

    protected $casts = [
        'data_hora_inicio' => 'datetime',
        'data_hora_fim' => 'datetime',
    ];

    public function paciente(): BelongsTo {
        return $this->belongsTo(Paciente::class);
    }

    public function profissional(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sala(): BelongsTo {
        return $this->belongsTo(Sala::class);
    }

    public function evolucao(): HasOne {
        return $this->hasOne(Evolucao::class);
    }
}
