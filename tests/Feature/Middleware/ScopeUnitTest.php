<?php

namespace Tests\Feature\Middleware;

use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ScopeUnitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerTestRoute();
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    #[Test]
    public function profissional_sem_unidade_selecionada_recebe_primeira_da_lista(): void
    {
        $user = User::factory()->create();
        $this->assignRole($user, 'Profissional');

        $unidades = Unidade::factory()->count(2)->create();
        $user->unidades()->attach($unidades->pluck('id'));

        $response = $this->actingAs($user)
            ->withSession([])
            ->get('/__scope-unit-test');

        $response->assertOk();
        $response->assertSessionHas('unidade_selecionada', $unidades->first()->id);
    }

    #[Test]
    public function profissional_com_unidade_invalida_no_session_recebe_primeira_unidade_valida(): void
    {
        $user = User::factory()->create();
        $this->assignRole($user, 'Profissional');

        $unidades = Unidade::factory()->count(2)->create();
        $user->unidades()->attach($unidades->pluck('id'));

        $response = $this->actingAs($user)
            ->withSession(['unidade_selecionada' => 9999])
            ->get('/__scope-unit-test');

        $response->assertOk();
        $response->assertSessionHas('unidade_selecionada', $unidades->first()->id);
    }

    #[Test]
    public function profissional_sem_unidades_remove_unidade_da_sessao(): void
    {
        $user = User::factory()->create();
        $this->assignRole($user, 'Profissional');

        $response = $this->actingAs($user)
            ->withSession(['unidade_selecionada' => 123])
            ->get('/__scope-unit-test');

        $response->assertOk();
        $response->assertSessionMissing('unidade_selecionada');
    }

    #[Test]
    public function admin_com_unidade_inexistente_nao_cria_nova_selecao(): void
    {
        $user = User::factory()->create();
        $this->assignRole($user, 'Admin');

        $response = $this->actingAs($user)
            ->withSession(['unidade_selecionada' => 9999])
            ->get('/__scope-unit-test');

        $response->assertOk();
        $response->assertSessionMissing('unidade_selecionada');
    }

    #[Test]
    public function admin_com_unidade_valida_mantem_a_selecao(): void
    {
        $user = User::factory()->create();
        $this->assignRole($user, 'Admin');

        $unidade = Unidade::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['unidade_selecionada' => $unidade->id])
            ->get('/__scope-unit-test');

        $response->assertOk();
        $response->assertSessionHas('unidade_selecionada', $unidade->id);
    }

    protected function registerTestRoute(): void
    {
        Route::middleware(['web', 'scope.unit'])
            ->get('/__scope-unit-test', fn () => response()->json(['ok' => true]));
    }

    protected function assignRole(User $user, string $roleName): void
    {
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        $user->assignRole($role);
    }
}


