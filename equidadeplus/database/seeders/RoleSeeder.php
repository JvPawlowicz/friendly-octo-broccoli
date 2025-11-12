<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// Importe o modelo de Papel
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa o cache de papéis (boa prática)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crie os 4 papéis do seu blueprint (Item 1.2)
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Coordenador']);
        Role::create(['name' => 'Profissional']);
        Role::create(['name' => 'Secretaria']);
    }
}

