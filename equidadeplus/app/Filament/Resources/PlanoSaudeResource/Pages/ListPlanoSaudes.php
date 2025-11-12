<?php

namespace App\Filament\Resources\PlanoSaudeResource\Pages;

use App\Filament\Resources\PlanoSaudeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanoSaudes extends ListRecords
{
    protected static string $resource = PlanoSaudeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
