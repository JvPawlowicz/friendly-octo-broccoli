<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'logo_unidade',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'telefone_principal',
    ];

    /**
     * Uma Unidade TEM MUITAS Salas.
     */
    public function salas(): HasMany
    {
        return $this->hasMany(Sala::class);
    }

    /**
     * Uma Unidade tem MUITOS Utilizadores (Profissionais, Secretárias...)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'unidade_user');
    }
}
