<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        // Identificação
        'foto_perfil', 'nome_completo', 'nome_social', 'data_nascimento', 
        'cpf', 'email_principal', 'telefone_principal', 'status',
        // Vínculos
        'unidade_padrao_id', 'plano_saude_id', 'numero_carteirinha', 
        'validade_carteirinha', 'tipo_plano',
        // Clínico
        'diagnostico_condicao', 'plano_de_crise', 'alergias_medicacoes', 
        'metodo_comunicacao', 'informacoes_escola', 'informacoes_medicas_adicionais',
        // Endereço
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
        // Configurações de Paciente Padrão
        'contar_como_atendimento', 'unidades_permitidas', 'tipos_agenda_permitidos', 'descricao_uso',
    ];

    /**
     * Define o "casting" para datas
     */
    protected $casts = [
        'data_nascimento' => 'date',
        'validade_carteirinha' => 'date',
        'contar_como_atendimento' => 'boolean',
        'unidades_permitidas' => 'array',
        'tipos_agenda_permitidos' => 'array',
    ];

    /**
     * Um Paciente TEM MUITOS Responsáveis.
     */
    public function responsaveis(): HasMany
    {
        return $this->hasMany(Responsavel::class);
    }

    /**
     * Um Paciente PERTENCE A um Plano de Saúde.
     */
    public function planoSaude(): BelongsTo
    {
        return $this->belongsTo(PlanoSaude::class);
    }

    /**
     * Um Paciente PERTENCE A uma Unidade Padrão.
     */
    public function unidadePadrao(): BelongsTo
    {
        return $this->belongsTo(Unidade::class, 'unidade_padrao_id');
    }

    /**
     * Um Paciente TEM MUITOS Atendimentos.
     */
    public function atendimentos(): HasMany
    {
        return $this->hasMany(Atendimento::class);
    }

    /**
     * Um Paciente TEM MUITOS Documentos.
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * Um Paciente TEM MUITAS Evoluções.
     */
    public function evolucoes(): HasMany
    {
        return $this->hasMany(Evolucao::class);
    }

    /**
     * Um Paciente TEM MUITAS Avaliações.
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    /**
     * Verifica se é o paciente padrão do sistema (para horários vagos/reuniões)
     */
    public function isPacientePadrao(): bool
    {
        return $this->cpf === '00000000000';
    }

    /**
     * Busca o primeiro paciente padrão do sistema (método de compatibilidade)
     */
    public static function pacientePadrao(): ?self
    {
        return static::where('cpf', '00000000000')->first();
    }

    /**
     * Busca todos os pacientes padrão do sistema
     */
    public static function pacientesPadrao()
    {
        return static::where('cpf', '00000000000')->where('status', 'Ativo')->get();
    }
}
