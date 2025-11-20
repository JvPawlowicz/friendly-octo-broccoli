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
        Schema::table('pacientes', function (Blueprint $table) {
            // Configurações específicas para pacientes padrão
            $table->boolean('contar_como_atendimento')->default(true)->after('status');
            $table->json('unidades_permitidas')->nullable()->after('contar_como_atendimento'); // IDs das unidades permitidas
            $table->json('tipos_agenda_permitidos')->nullable()->after('unidades_permitidas'); // Tipos de agenda permitidos (ex: ['reuniao', 'horario_vago', 'capacitacao'])
            $table->text('descricao_uso')->nullable()->after('tipos_agenda_permitidos'); // Descrição de como usar este paciente padrão
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn([
                'contar_como_atendimento',
                'unidades_permitidas',
                'tipos_agenda_permitidos',
                'descricao_uso',
            ]);
        });
    }
};
