<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Unidade;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProntuarioAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_profissional_da_mesma_unidade_acessa_prontuario(): void
    {
        $unidade = Unidade::factory()->create();

        $profissional = User::factory()->create();
        $profissional->assignRole('Profissional');
        $profissional->unidades()->attach($unidade->id);

        $paciente = Paciente::factory()->create([
            'unidade_padrao_id' => $unidade->id,
        ]);

        $response = $this->actingAs($profissional)->get("/app/pacientes/{$paciente->id}/prontuario");
        $response->assertOk();
        $response->assertSee($paciente->nome_completo);
    }

    public function test_profissional_de_outra_unidade_recebe_403(): void
    {
        $unidadeA = Unidade::factory()->create();
        $unidadeB = Unidade::factory()->create();

        $profissional = User::factory()->create();
        $profissional->assignRole('Profissional');
        $profissional->unidades()->attach($unidadeA->id);

        $paciente = Paciente::factory()->create([
            'unidade_padrao_id' => $unidadeB->id,
        ]);

        $response = $this->actingAs($profissional)->get("/app/pacientes/{$paciente->id}/prontuario");
        $response->assertForbidden();
    }
}


