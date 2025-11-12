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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            
            // Ligação: Um Documento PERTENCE a um Paciente
            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->onDelete('cascade');
            
            // Ligação: Quem fez o upload
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->string('titulo_documento');
            $table->string('path_arquivo'); // Caminho do arquivo no storage
            $table->string('categoria')->nullable(); // Ex: Laudo, Exame, Receita
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
