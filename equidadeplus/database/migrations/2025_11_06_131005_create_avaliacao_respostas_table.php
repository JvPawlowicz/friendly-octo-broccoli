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
        Schema::create('avaliacao_respostas', function (Blueprint $table) {
            $table->id();
            
            // Ligação 1: A qual Avaliação esta resposta pertence
            $table->foreignId('avaliacao_id')
                  ->constrained('avaliacaos') // Liga com a tabela acima
                  ->onDelete('cascade');
            
            // Ligação 2: A qual Pergunta esta resposta se refere
            $table->foreignId('avaliacao_pergunta_id')
                  ->constrained('avaliacao_perguntas') // Liga com o Prompt 5
                  ->onDelete('cascade');

            // O campo "RESPOSTA"
            // Usamos 'text' para ser flexível (guarda texto, data, ou "true"/"false")
            $table->text('resposta')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacao_respostas');
    }
};
