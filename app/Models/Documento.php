<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'user_id',
        'titulo_documento',
        'path_arquivo',
        'categoria',
    ];

    /**
     * Um Documento PERTENCE A um Paciente.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Um Documento PERTENCE A um User (quem fez upload).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias para compatibilidade
     */
    public function usuario(): BelongsTo
    {
        return $this->user();
    }
}
