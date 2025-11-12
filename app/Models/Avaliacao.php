<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacaos';

    protected $fillable = [
        'paciente_id',
        'user_id',
        'avaliacao_template_id',
        'status',
    ];

    /**
     * Uma Avaliação (aplicada) PERTENCE A um Paciente.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Uma Avaliação (aplicada) PERTENCE A um Profissional (User).
     */
    public function profissional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Uma Avaliação (aplicada) usou um Template.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(AvaliacaoTemplate::class, 'avaliacao_template_id');
    }

    /**
     * Uma Avaliação (aplicada) TEM MUITAS Respostas.
     */
    public function respostas(): HasMany
    {
        return $this->hasMany(AvaliacaoResposta::class);
    }
}
