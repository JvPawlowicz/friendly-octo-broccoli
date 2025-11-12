<?php

namespace Tests\Feature;

use App\Models\Unidade;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_profissional_acessa_dashboard(): void
    {
        $unidade = Unidade::factory()->create();

        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('Profissional');
        $user->unidades()->attach($unidade->id);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertSee('Painel Clínico');
    }

    public function test_secretaria_recebe_403_no_dashboard(): void
    {
        $unidade = Unidade::factory()->create();

        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('Secretaria');
        $user->unidades()->attach($unidade->id);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertForbidden();
    }
}


