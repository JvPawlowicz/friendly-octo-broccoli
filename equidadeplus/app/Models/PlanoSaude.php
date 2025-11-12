<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanoSaude extends Model
{
    use HasFactory;

    // Define o nome da tabela explicitamente (boa prática)
    protected $table = 'plano_saudes';

    protected $fillable = [
        'nome_plano',
        'codigo_ans',
        'status',
    ];

    /**
     * Define o "casting" para o campo status.
     * Isto diz ao Laravel para tratar o 'status' como booleano (true/false)
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Um Plano de Saúde TEM MUITOS Pacientes.
     */
    public function pacientes(): HasMany
    {
        return $this->hasMany(\App\Models\Paciente::class, 'plano_saude_id');
    }
}
