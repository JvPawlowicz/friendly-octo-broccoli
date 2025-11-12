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
        Schema::create('plano_saudes', function (Blueprint $table) {
            $table->id();
            
            // Ex: "Unimed", "Bradesco Saúde"
            $table->string('nome_plano'); 
            
            // Código de registo da ANS
            $table->string('codigo_ans')->nullable(); 
            
            // Para "desativar" um plano sem o apagar
            $table->boolean('status')->default(true); // true = Ativo, false = Inativo

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_saudes');
    }
};
