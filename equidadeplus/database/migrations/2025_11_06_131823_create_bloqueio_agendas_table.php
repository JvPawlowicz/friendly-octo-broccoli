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
        Schema::create('bloqueio_agendas', function (Blueprint $table) {
            $table->id();
            
            $table->string('titulo_bloqueio'); // Ex: "Feriado", "Reunião"

            $table->dateTime('data_hora_inicio');
            $table->dateTime('data_hora_fim');

            // --- Bloqueios específicos ---
            // (Se todos forem null, é um bloqueio global)

            // Bloqueia uma Unidade inteira
            $table->foreignId('unidade_id')->nullable()->constrained('unidades');
            // Bloqueia um Profissional (férias)
            $table->foreignId('user_id')->nullable()->constrained('users');
            // Bloqueia uma Sala (manutenção)
            $table->foreignId('sala_id')->nullable()->constrained('salas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloqueio_agendas');
    }
};
