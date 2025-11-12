<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvaliacaoTemplate extends Model
{
    use HasFactory;

    protected $table = 'avaliacao_templates';

    protected $fillable = [
        'nome_template',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Um Template TEM MUITAS Perguntas.
     * Usamos 'orderBy' para garantir que elas venham na ordem correta.
     */
    public function perguntas(): HasMany
    {
        return $this->hasMany(AvaliacaoPergunta::class)->orderBy('ordem');
    }

    /**
     * Um Template TEM MUITAS Avaliações (aplicadas).
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(\App\Models\Avaliacao::class, 'avaliacao_template_id');
    }
}
