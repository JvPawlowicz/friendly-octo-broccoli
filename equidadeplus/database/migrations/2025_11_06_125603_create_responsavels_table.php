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
        Schema::create('responsaveis', function (Blueprint $table) {
            $table->id();
            
            // Ligação: Um Responsável PERTENCE a um Paciente
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade'); // Se apagar o paciente, apaga os responsáveis

            $table->string('nome_completo');
            $table->string('parentesco'); // Ex: "Mãe", "Pai", "Guardião Legal"
            $table->string('email')->nullable();
            $table->string('telefone_principal');
            $table->string('cpf', 14)->nullable();

            // Checkboxes (Item 4.1)
            $table->boolean('is_responsavel_legal')->default(false);
            $table->boolean('is_contato_emergencia')->default(false);
            $table->boolean('recebe_comunicacoes')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsaveis');
    }
};
