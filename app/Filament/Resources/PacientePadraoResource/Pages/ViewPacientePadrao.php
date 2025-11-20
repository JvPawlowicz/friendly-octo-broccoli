<?php

namespace App\Filament\Resources\PacientePadraoResource\Pages;

use App\Filament\Resources\PacientePadraoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPacientePadrao extends ViewRecord
{
    protected static string $resource = PacientePadraoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
