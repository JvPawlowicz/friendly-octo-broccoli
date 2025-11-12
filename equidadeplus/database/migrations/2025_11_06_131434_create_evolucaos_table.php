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
        Schema::create('evolucoes', function (Blueprint $table) {
            $table->id();
            
            // Ligação 1: Quem foi atendido (Paciente)
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade');
            
            // Ligação 2: Quem escreveu (Profissional)
            $table->foreignId('user_id')
                  ->constrained('users') // Liga com a tabela 'users'
                  ->onDelete('cascade'); 
            
            // Ligação 3: De qual atendimento (Item 5.1)
            // Será nullable, pois nem toda evolução vem de um atendimento
            // Não podemos usar constrained() ainda, pois a tabela 'atendimentos' não existe
            $table->unsignedBigInteger('atendimento_id')->nullable(); 

            // Ligação 4: Adendo (Item 5.2)
            // Se esta evolução for um "adendo", ela aponta para a evolução "pai"
            $table->foreignId('evolucao_pai_id')
                  ->nullable()
                  ->constrained('evolucoes') // Liga com ela mesma
                  ->onDelete('cascade');

            // Campos da Evolução (Item 5.2)
            $table->text('relato_clinico')->nullable();
            $table->text('conduta')->nullable();
            $table->text('objetivos')->nullable();

            // Status da Evolução (Item 5.2)
            $table->string('status'); // Ex: "Rascunho", "Finalizado"
            
            // "Assinatura" (Item 5.2) - Data de finalização
            $table->timestamp('finalizado_em')->nullable(); 

            $table->timestamps(); // 'created_at' será a data da evolução
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evolucoes');
    }
};
