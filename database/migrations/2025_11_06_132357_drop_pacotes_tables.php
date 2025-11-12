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
        // Remove as tabelas na ordem correta (filho primeiro)
        Schema::dropIfExists('atendimento_pacote');
        Schema::dropIfExists('pacotes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se quiséssemos reverter, recriaríamos as tabelas
        // (Não é necessário preencher o "down" neste caso)
    }
};
