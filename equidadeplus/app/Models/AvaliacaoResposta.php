<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvaliacaoResposta extends Model
{
    use HasFactory;

    protected $table = 'avaliacao_respostas';

    protected $fillable = [
        'avaliacao_id',
        'avaliacao_pergunta_id',
        'resposta',
    ];

    /**
     * Uma Resposta PERTENCE A uma Avaliação (aplicada).
     */
    public function avaliacao(): BelongsTo
    {
        return $this->belongsTo(Avaliacao::class);
    }

    /**
     * Uma Resposta refere-se a uma Pergunta (do template).
     */
    public function pergunta(): BelongsTo
    {
        return $this->belongsTo(AvaliacaoPergunta::class, 'avaliacao_pergunta_id');
    }
}
