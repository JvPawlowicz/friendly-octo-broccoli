<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvaliacaoPergunta extends Model
{
    use HasFactory;

    protected $table = 'avaliacao_perguntas';

    protected $fillable = [
        'avaliacao_template_id',
        'titulo_pergunta',
        'tipo_campo',
        'ordem',
    ];

    /**
     * Uma Pergunta PERTENCE A um Template.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(AvaliacaoTemplate::class, 'avaliacao_template_id');
    }
}
