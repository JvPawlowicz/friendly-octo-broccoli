<?php

namespace Database\Seeders;

use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\PlanoSaude;
use App\Models\Sala;
use App\Models\Unidade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CoreDemoSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $unidade = Unidade::factory()->create([
            'nome' => 'Clínica Equidade+ Central',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'telefone_principal' => '(11) 0000-0000',
        ]);

        $sala = Sala::factory()->create([
            'unidade_id' => $unidade->id,
            'nome' => 'Sala Terapia 01',
        ]);

        $plano = PlanoSaude::firstOrCreate(
            ['nome_plano' => 'Plano Saúde Demo'],
            ['codigo_ans' => '123456', 'status' => true]
        );

        $admin = $this->createUser(
            name: 'Administrador Master',
            email: 'admin@equidade.test',
            password: 'Admin123!',
            role: 'Admin',
            unidadeId: $unidade->id
        );

        $coordenador = $this->createUser(
            name: 'Carla Coordenadora',
            email: 'coordenacao@equidade.test',
            password: 'Coordenador123!',
            role: 'Coordenador',
            unidadeId: $unidade->id
        );

        $profissional = $this->createUser(
            name: 'Pedro Profissional',
            email: 'profissional@equidade.test',
            password: 'Profissional123!',
            role: 'Profissional',
            unidadeId: $unidade->id
        );

        $secretaria = $this->createUser(
            name: 'Sara Secretaria',
            email: 'secretaria@equidade.test',
            password: 'Secretaria123!',
            role: 'Secretaria',
            unidadeId: $unidade->id
        );

        $paciente = Paciente::factory()->create([
            'nome_completo' => 'Lucas Paciente Demo',
            'email_principal' => 'paciente@equidade.test',
            'telefone_principal' => '(11) 99999-0000',
            'unidade_padrao_id' => $unidade->id,
            'plano_saude_id' => $plano->id,
        ]);

        $inicio = Carbon::now()->startOfWeek()->addDays(2)->setTime(9, 0);
        Atendimento::create([
            'paciente_id' => $paciente->id,
            'user_id' => $profissional->id,
            'sala_id' => $sala->id,
            'status' => 'Agendado',
            'data_hora_inicio' => $inicio,
            'data_hora_fim' => (clone $inicio)->addHour(),
            'recorrencia_id' => Str::uuid()->toString(),
        ]);

        $this->command->info('Credenciais padrão:');
        $this->command->table(
            ['Perfil', 'Email', 'Senha'],
            [
                ['Admin', $admin->email, 'Admin123!'],
                ['Coordenador', $coordenador->email, 'Coordenador123!'],
                ['Profissional', $profissional->email, 'Profissional123!'],
                ['Secretaria', $secretaria->email, 'Secretaria123!'],
                ['Paciente (login portal externo)', $paciente->email_principal, '-'],
            ]
        );
    }

    protected function createUser(string $name, string $email, string $password, string $role, int $unidadeId): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->assignRole($role);
        $user->unidades()->syncWithoutDetaching([$unidadeId]);

        return $user;
    }
}


