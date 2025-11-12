<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisponibilidadeUsuario extends Model
{
    use HasFactory;

    protected $table = 'disponibilidade_usuarios'; // Boa prática

    protected $fillable = [
        'user_id',
        'dia_da_semana',
        'hora_inicio',
        'hora_fim',
    ];

    /**
     * Uma disponibilidade PERTENCE A um Utilizador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
