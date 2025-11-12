<?php

namespace Tests\Feature;

use App\Models\Unidade;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_profissional_acessa_agenda(): void
    {
        $unidade = Unidade::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('Profissional');
        $user->unidades()->attach($unidade->id);

        $response = $this->actingAs($user)->get('/app/agenda');
        $response->assertOk();
        $response->assertSee('Agenda Integrada');
    }

    public function test_usuario_sem_permissao_recebe_403_na_agenda(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/app/agenda');
        $response->assertForbidden();
    }
}


