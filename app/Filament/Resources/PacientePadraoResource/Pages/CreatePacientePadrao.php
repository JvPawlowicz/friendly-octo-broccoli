<?php

namespace App\Filament\Resources\PacientePadraoResource\Pages;

use App\Filament\Resources\PacientePadraoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePacientePadrao extends CreateRecord
{
    protected static string $resource = PacientePadraoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Garante que o CPF seja sempre '00000000000' para pacientes padrão
        $data['cpf'] = '00000000000';
        $data['status'] = $data['status'] ?? 'Ativo';
        $data['contar_como_atendimento'] = $data['contar_como_atendimento'] ?? true;
        
        return $data;
    }
}
