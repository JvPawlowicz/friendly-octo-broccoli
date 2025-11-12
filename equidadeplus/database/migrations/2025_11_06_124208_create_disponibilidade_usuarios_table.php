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
        Schema::create('disponibilidade_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Opcional: A disponibilidade pode ser por unidade
            // $table->foreignId('unidade_id')->constrained()->onDelete('cascade'); 

            // 0=Domingo, 1=Segunda, 2=Terça, ... 6=Sábado
            $table->integer('dia_da_semana'); 
            
            $table->time('hora_inicio'); // Ex: "08:00:00"
            $table->time('hora_fim');    // Ex: "12:00:00"
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilidade_usuarios');
    }
};
