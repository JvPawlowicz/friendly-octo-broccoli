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
        Schema::create('avaliacao_perguntas', function (Blueprint $table) {
            $table->id();
            
            // Ligação: Uma Pergunta PERTENCE a um Template
            $table->foreignId('avaliacao_template_id')
                  ->constrained('avaliacao_templates')
                  ->onDelete('cascade'); // Se apagar o template, apaga as perguntas

            // Ex: "Histórico da criança", "Medicações atuais"
            $table->string('titulo_pergunta'); 
            
            // A nossa "Opção A" simplificada
            $table->string('tipo_campo'); // 'texto_curto', 'texto_longo', 'data', 'sim_nao'
            
            // Para ordenar as perguntas no formulário
            $table->integer('ordem')->default(0); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacao_perguntas');
    }
};
