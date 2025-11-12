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
        Schema::create('pacotes', function (Blueprint $table) {
            $table->id();
            
            // O Pacote pertence a um Paciente
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade');
            
            // Ex: "Pacote Fono 10 Sessões", "Pacote T.O 5 Sessões"
            $table->string('nome_pacote');
            
            $table->string('status')->default('Ativo'); // Ativo, Concluído

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacotes');
    }
};
