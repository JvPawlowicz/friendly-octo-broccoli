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
        Schema::create('avaliacaos', function (Blueprint $table) {
            $table->id();
            
            // Ligação 1: Quem foi avaliado (Paciente)
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade');
            
            // Ligação 2: Quem aplicou (Profissional)
            $table->foreignId('user_id')
                  ->constrained('users') // Liga com a tabela 'users'
                  ->onDelete('cascade'); 
            
            // Ligação 3: Qual modelo foi usado
            $table->foreignId('avaliacao_template_id')
                  ->constrained('avaliacao_templates') // Liga com o Prompt 5
                  ->onDelete('cascade');

            // Status da Avaliação (Item 5.2)
            $table->string('status'); // Ex: "Rascunho", "Finalizado"

            $table->timestamps(); // 'created_at' será a data da aplicação
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacaos');
    }
};
