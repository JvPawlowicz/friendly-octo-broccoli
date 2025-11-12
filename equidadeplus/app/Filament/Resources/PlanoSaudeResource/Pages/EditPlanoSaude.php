<?php

namespace App\Filament\Resources\PlanoSaudeResource\Pages;

use App\Filament\Resources\PlanoSaudeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanoSaude extends EditRecord
{
    protected static string $resource = PlanoSaudeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
