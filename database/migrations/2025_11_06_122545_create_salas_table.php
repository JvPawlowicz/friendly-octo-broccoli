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
        Schema::create('salas', function (Blueprint $table) {
            $table->id();

            // Esta é a ligação: Uma sala PERTENCE a uma Unidade
            $table->foreignId('unidade_id')
                  ->constrained('unidades') // Liga com a tabela 'unidades'
                  ->onDelete('cascade');    // Se apagar a unidade, apaga as salas

            $table->string('nome'); // Ex: "Sala Terapia Ocupacional", "Sala 1"
            $table->integer('capacidade')->default(1); // Item 8.2
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salas');
    }
};
