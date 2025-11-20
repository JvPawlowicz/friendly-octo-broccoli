<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\Unidade;
use Illuminate\Database\Seeder;

class PacientePadraoSeeder extends Seeder
{
    /**
     * Cria o paciente padrão do sistema para horários vagos/reuniões
     */
    public function run(): void
    {
        // Busca a primeira unidade ou cria uma padrão
        $unidade = Unidade::first();
        
        if (!$unidade) {
            $unidade = Unidade::create([
                'nome' => 'Unidade Padrão',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
            ]);
        }

        // Cria ou atualiza o paciente padrão
        Paciente::updateOrCreate(
            [
                'cpf' => '00000000000', // CPF especial para identificar paciente padrão
            ],
            [
                'nome_completo' => 'Horário Vago / Reunião',
                'nome_social' => 'Horário Vago',
                'status' => 'Ativo',
                'unidade_padrao_id' => $unidade->id,
                'email_principal' => null,
                'telefone_principal' => null,
                'data_nascimento' => null,
                'contar_como_atendimento' => false, // Por padrão, não conta como atendimento
                'unidades_permitidas' => null, // null = todas as unidades
                'tipos_agenda_permitidos' => ['horario_vago', 'reuniao'], // Tipos permitidos
                'descricao_uso' => 'Use este paciente padrão para bloquear horários vagos, agendar reuniões internas, capacitações e outros eventos que não são atendimentos clínicos.',
            ]
        );
    }
}

