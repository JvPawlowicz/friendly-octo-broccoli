<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evolucao extends Model
{
    use HasFactory;

    protected $table = 'evolucoes';

    protected $fillable = [
        'paciente_id',
        'user_id',
        'atendimento_id',
        'evolucao_pai_id',
        'relato_clinico',
        'conduta',
        'objetivos',
        'status',
        'finalizado_em',
    ];

    /**
     * Define o "casting" para datas
     */
    protected $casts = [
        'finalizado_em' => 'datetime',
    ];

    /**
     * Uma Evolução PERTENCE A um Paciente.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Uma Evolução PERTENCE A um Profissional (User).
     */
    public function profissional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // --- Relações de Adendo (Item 5.2) ---

    /**
     * Uma Evolução (Adendo) PERTENCE A uma Evolução (Pai).
     */
    public function evolucaoPai(): BelongsTo
    {
        // Relação "dela com ela mesma"
        return $this->belongsTo(Evolucao::class, 'evolucao_pai_id');
    }

    /**
     * Uma Evolução (Pai) TEM MUITOS Adendos.
     */
    public function adendos(): HasMany
    {
        return $this->hasMany(Evolucao::class, 'evolucao_pai_id')->orderBy('created_at');
    }

    /**
     * Uma Evolução PERTENCE A um Atendimento (Item 5.1).
     */
    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class);
    }
}
