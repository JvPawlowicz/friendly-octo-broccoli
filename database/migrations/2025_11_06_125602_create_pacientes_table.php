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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            // --- A. Dados de Identificação ---
            $table->string('foto_perfil')->nullable();
            $table->string('nome_completo');
            $table->string('nome_social')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('cpf', 14)->nullable()->unique();
            $table->string('email_principal')->nullable();
            $table->string('telefone_principal')->nullable();
            $table->string('status')->default('Ativo'); // Ativo, Inativo, Em espera
            
            // --- B. Vínculos do Sistema ---
            $table->foreignId('unidade_padrao_id') // Unidade principal
                  ->nullable()
                  ->constrained('unidades') // Liga com a tabela 'unidades'
                  ->onDelete('set null');

            $table->foreignId('plano_saude_id') // Plano de saúde
                  ->nullable()
                  ->constrained('plano_saudes') // Liga com a tabela 'plano_saudes'
                  ->onDelete('set null');

            $table->string('numero_carteirinha')->nullable();
            $table->date('validade_carteirinha')->nullable();
            $table->string('tipo_plano')->nullable(); // Ex: Apartamento

            // --- C. Dados Clínicos (Item 4.1) ---
            $table->text('diagnostico_condicao')->nullable();
            $table->text('plano_de_crise')->nullable();
            $table->text('alergias_medicacoes')->nullable();
            $table->string('metodo_comunicacao')->nullable();
            $table->text('informacoes_escola')->nullable();
            $table->text('informacoes_medicas_adicionais')->nullable();

            // --- D. Endereço (Item 4.1 Expansão) ---
            $table->string('cep', 10)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
