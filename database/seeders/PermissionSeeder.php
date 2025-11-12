<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Cria Permissões Granulares conforme o Master Prompt
     */
    public function run(): void
    {
        // Limpa o cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================
        // PERMISSÕES GRANULARES (Item Módulo 0)
        // ============================================
        
        // --- Gestão de Agenda ---
        $permissions = [
            'ver_agenda_unidade',
            'editar_agenda_unidade',
            'cancelar_atendimento',
            'confirmar_atendimento',
            
            // --- Gestão de Pacientes ---
            'ver_pacientes',
            'criar_paciente',
            'editar_paciente',
            'apagar_paciente',
            'ver_prontuario',
            
            // --- Gestão de Avaliações ---
            'aplicar_avaliacao',
            'ver_avaliacoes',
            'finalizar_avaliacao',
            
            // --- Gestão de Evoluções ---
            'criar_evolucao',
            'editar_evolucao',
            'finalizar_evolucao',
            'ver_evolucoes',
            
            // --- Relatórios ---
            'ver_relatorios',
            'exportar_relatorios',
            
            // --- Gestão de Documentos ---
            'upload_documento',
            'apagar_documento',
            'ver_documentos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ============================================
        // ATRIBUIR PERMISSÕES AOS PAPÉIS
        // ============================================
        
        // Admin: Super Admin (ignora todas as permissões via Filament)
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        
        // Coordenador: Acesso amplo, mas não é Super Admin
        $coordenadorRole = Role::firstOrCreate(['name' => 'Coordenador']);
        $coordenadorRole->givePermissionTo([
            'ver_agenda_unidade',
            'editar_agenda_unidade',
            'ver_pacientes',
            'ver_prontuario',
            'aplicar_avaliacao',
            'ver_avaliacoes',
            'finalizar_avaliacao',
            'ver_evolucoes',
            'ver_relatorios',
            'exportar_relatorios',
            'ver_documentos',
        ]);
        
        // Profissional: Acesso operacional
        $profissionalRole = Role::firstOrCreate(['name' => 'Profissional']);
        $profissionalRole->givePermissionTo([
            'ver_agenda_unidade',
            'editar_agenda_unidade',
            'confirmar_atendimento',
            'cancelar_atendimento',
            'ver_pacientes',
            'ver_prontuario',
            'aplicar_avaliacao',
            'ver_avaliacoes',
            'finalizar_avaliacao',
            'criar_evolucao',
            'editar_evolucao',
            'finalizar_evolucao',
            'ver_evolucoes',
            'upload_documento',
            'ver_documentos',
        ]);
        
        // Secretaria: Acesso administrativo limitado
        $secretariaRole = Role::firstOrCreate(['name' => 'Secretaria']);
        $secretariaRole->givePermissionTo([
            'ver_agenda_unidade',
            'editar_agenda_unidade',
            'confirmar_atendimento',
            'cancelar_atendimento',
            'ver_pacientes',
            'criar_paciente',
            'editar_paciente',
            'upload_documento',
            'ver_documentos',
        ]);
    }
}
