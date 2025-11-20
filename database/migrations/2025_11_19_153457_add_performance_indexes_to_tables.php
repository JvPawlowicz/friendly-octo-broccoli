<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices para tabela atendimentos
        Schema::table('atendimentos', function (Blueprint $table) {
            $table->index('data_hora_inicio', 'idx_atendimentos_data_hora_inicio');
            $table->index('status', 'idx_atendimentos_status');
            $table->index('user_id', 'idx_atendimentos_user_id');
            $table->index('paciente_id', 'idx_atendimentos_paciente_id');
            $table->index('sala_id', 'idx_atendimentos_sala_id');
            $table->index('recorrencia_id', 'idx_atendimentos_recorrencia_id');
        });

        // Índices para tabela evolucoes
        Schema::table('evolucoes', function (Blueprint $table) {
            $table->index('status', 'idx_evolucoes_status');
            $table->index('user_id', 'idx_evolucoes_user_id');
            $table->index('paciente_id', 'idx_evolucoes_paciente_id');
            $table->index('atendimento_id', 'idx_evolucoes_atendimento_id');
            $table->index('evolucao_pai_id', 'idx_evolucoes_pai_id');
            $table->index('created_at', 'idx_evolucoes_created_at');
        });

        // Índices para tabela avaliacoes
        Schema::table('avaliacaos', function (Blueprint $table) {
            $table->index('status', 'idx_avaliacoes_status');
            $table->index('user_id', 'idx_avaliacoes_user_id');
            $table->index('paciente_id', 'idx_avaliacoes_paciente_id');
            $table->index('avaliacao_template_id', 'idx_avaliacoes_template_id');
            $table->index('created_at', 'idx_avaliacoes_created_at');
        });

        // Índices para tabela pacientes
        Schema::table('pacientes', function (Blueprint $table) {
            $table->index('unidade_padrao_id', 'idx_pacientes_unidade_padrao_id');
            $table->index('cpf', 'idx_pacientes_cpf');
            $table->index('nome_completo', 'idx_pacientes_nome_completo');
        });

        // Índices para tabela bloqueio_agendas
        Schema::table('bloqueio_agendas', function (Blueprint $table) {
            $table->index('data_hora_inicio', 'idx_bloqueio_data_hora_inicio');
            $table->index('data_hora_fim', 'idx_bloqueio_data_hora_fim');
            $table->index('user_id', 'idx_bloqueio_user_id');
        });

        // Índices para tabela disponibilidade_usuarios
        Schema::table('disponibilidade_usuarios', function (Blueprint $table) {
            $table->index('user_id', 'idx_disponibilidade_user_id');
            $table->index('dia_da_semana', 'idx_disponibilidade_dia_semana');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atendimentos', function (Blueprint $table) {
            $table->dropIndex('idx_atendimentos_data_hora_inicio');
            $table->dropIndex('idx_atendimentos_status');
            $table->dropIndex('idx_atendimentos_user_id');
            $table->dropIndex('idx_atendimentos_paciente_id');
            $table->dropIndex('idx_atendimentos_sala_id');
            $table->dropIndex('idx_atendimentos_recorrencia_id');
        });

        Schema::table('evolucoes', function (Blueprint $table) {
            $table->dropIndex('idx_evolucoes_status');
            $table->dropIndex('idx_evolucoes_user_id');
            $table->dropIndex('idx_evolucoes_paciente_id');
            $table->dropIndex('idx_evolucoes_atendimento_id');
            $table->dropIndex('idx_evolucoes_pai_id');
            $table->dropIndex('idx_evolucoes_created_at');
        });

        Schema::table('avaliacaos', function (Blueprint $table) {
            $table->dropIndex('idx_avaliacoes_status');
            $table->dropIndex('idx_avaliacoes_user_id');
            $table->dropIndex('idx_avaliacoes_paciente_id');
            $table->dropIndex('idx_avaliacoes_template_id');
            $table->dropIndex('idx_avaliacoes_created_at');
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropIndex('idx_pacientes_unidade_padrao_id');
            $table->dropIndex('idx_pacientes_cpf');
            $table->dropIndex('idx_pacientes_nome_completo');
        });

        Schema::table('bloqueio_agendas', function (Blueprint $table) {
            $table->dropIndex('idx_bloqueio_data_hora_inicio');
            $table->dropIndex('idx_bloqueio_data_hora_fim');
            $table->dropIndex('idx_bloqueio_user_id');
        });

        Schema::table('disponibilidade_usuarios', function (Blueprint $table) {
            $table->dropIndex('idx_disponibilidade_user_id');
            $table->dropIndex('idx_disponibilidade_dia_semana');
        });
    }
};
