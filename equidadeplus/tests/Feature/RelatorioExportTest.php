<?php

namespace Tests\Feature;

use App\Livewire\RelatorioFrequencia;
use App\Livewire\RelatorioProdutividade;
use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\Sala;
use App\Models\Unidade;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class RelatorioExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_exporta_relatorio_frequencia_em_csv(): void
    {
        [$coordenador] = $this->criarAtendimentosDemo();

        $this->actingAs($coordenador);
        $component = app(RelatorioFrequencia::class);
        $component->mount();

        $response = $component->exportar();

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_exporta_relatorio_produtividade_em_csv(): void
    {
        [$coordenador] = $this->criarAtendimentosDemo();

        $this->actingAs($coordenador);
        $component = app(RelatorioProdutividade::class);
        $component->mount();

        $response = $component->exportarCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    /**
     * @return array{0:\App\Models\User,1:\App\Models\User}
     */
    protected function criarAtendimentosDemo(): array
    {
        $unidade = Unidade::factory()->create();
        $sala = Sala::factory()->create(['unidade_id' => $unidade->id]);

        $coordenador = User::factory()->create();
        $coordenador->assignRole('Coordenador');
        $coordenador->unidades()->attach($unidade->id);

        $profissional = User::factory()->create();
        $profissional->assignRole('Profissional');
        $profissional->unidades()->attach($unidade->id);

        $paciente = Paciente::factory()->create([
            'unidade_padrao_id' => $unidade->id,
        ]);

        // Atendimentos concluído e cancelado nas últimas datas
        Atendimento::create([
            'paciente_id' => $paciente->id,
            'user_id' => $profissional->id,
            'sala_id' => $sala->id,
            'status' => 'Concluído',
            'data_hora_inicio' => now()->subDays(2)->setTime(9, 0),
            'data_hora_fim' => now()->subDays(2)->setTime(10, 0),
            'recorrencia_id' => null,
        ]);

        Atendimento::create([
            'paciente_id' => $paciente->id,
            'user_id' => $profissional->id,
            'sala_id' => $sala->id,
            'status' => 'Cancelado',
            'data_hora_inicio' => now()->subDay()->setTime(11, 0),
            'data_hora_fim' => now()->subDay()->setTime(12, 0),
            'recorrencia_id' => null,
        ]);

        return [$coordenador, $profissional];
    }
}


