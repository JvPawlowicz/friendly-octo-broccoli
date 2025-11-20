<?php

namespace App\Filament\Resources\PacientePadraoResource\Pages;

use App\Filament\Resources\PacientePadraoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPacientePadrao extends EditRecord
{
    protected static string $resource = PacientePadraoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Garante que o CPF permaneça '00000000000'
        $data['cpf'] = '00000000000';
        
        return $data;
    }
}
