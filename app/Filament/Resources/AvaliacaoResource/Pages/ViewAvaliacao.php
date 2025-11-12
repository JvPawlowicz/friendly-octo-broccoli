<?php

namespace App\Filament\Resources\AvaliacaoResource\Pages;

use App\Filament\Resources\AvaliacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAvaliacao extends ViewRecord
{
    protected static string $resource = AvaliacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

