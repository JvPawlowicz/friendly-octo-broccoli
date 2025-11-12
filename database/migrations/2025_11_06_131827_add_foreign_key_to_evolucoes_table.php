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
        Schema::table('evolucoes', function (Blueprint $table) {
            // Adiciona a "constraint" (regra) ao campo que já existe
            $table->foreign('atendimento_id')
                  ->references('id')
                  ->on('atendimentos')
                  ->onDelete('set null'); // Se apagar o atendimento, a evolução fica órfã
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evolucoes', function (Blueprint $table) {
            // Remove a "constraint" (o nome é gerado pelo Laravel)
            $table->dropForeign(['atendimento_id']);
        });
    }
};
