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
        Schema::create('atendimento_pacote', function (Blueprint $table) {
            $table->id();

            $table->foreignId('atendimento_id')
                  ->constrained('atendimentos')
                  ->onDelete('cascade');
            
            $table->foreignId('pacote_id')
                  ->constrained('pacotes')
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atendimento_pacote');
    }
};
