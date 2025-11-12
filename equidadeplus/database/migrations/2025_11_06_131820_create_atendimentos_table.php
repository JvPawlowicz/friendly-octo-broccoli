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
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            
            // Ligação 1: Quem é o Paciente
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade');
            
            // Ligação 2: Quem é o Profissional
            $table->foreignId('user_id')
                  ->constrained('users') // Profissional
                  ->onDelete('cascade');
            
            // Ligação 3: Onde (Sala)
            $table->foreignId('sala_id')
                  ->nullable() // Pode ser um atendimento online, sem sala
                  ->constrained('salas')
                  ->onDelete('set null');

            // Status do Atendimento (Item 3.2)
            $table->string('status'); // Agendado, Confirmado, Check-in, etc.

            // Horários
            $table->dateTime('data_hora_inicio');
            $table->dateTime('data_hora_fim');

            // Recorrência (Item 3.3)
            // Um ID (ex: UUID) que agrupa todos os eventos da mesma recorrência
            $table->string('recorrencia_id')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
