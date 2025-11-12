<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Responsavel extends Model
{
    use HasFactory;

    // Define o nome da tabela explicitamente (boa prática)
    protected $table = 'responsaveis';

    protected $fillable = [
        'paciente_id', 'nome_completo', 'parentesco', 'email', 
        'telefone_principal', 'cpf', 'is_responsavel_legal', 
        'is_contato_emergencia', 'recebe_comunicacoes',
    ];

    /**
     * Define o "casting" para booleanos
     */
    protected $casts = [
        'is_responsavel_legal' => 'boolean',
        'is_contato_emergencia' => 'boolean',
        'recebe_comunicacoes' => 'boolean',
    ];

    /**
     * Um Responsável PERTENCE A um Paciente.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }
}
