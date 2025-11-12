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
        Schema::create('avaliacao_templates', function (Blueprint $table) {
            $table->id();
            
            // Ex: "Anamnese", "Avaliação Periódica Fono"
            $table->string('nome_template'); 
            
            // Para "desativar" um template
            $table->boolean('status')->default(true); // true = Ativo

            // Futuramente, podemos ligar a uma unidade (Item 6.1)
            // $table->foreignId('unidade_id')->nullable()->constrained('unidades');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacao_templates');
    }
};
