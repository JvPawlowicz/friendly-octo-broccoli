<?php

namespace App\Filament\Resources\PacientePadraoResource\Pages;

use App\Filament\Resources\PacientePadraoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPacientePadraos extends ListRecords
{
    protected static string $resource = PacientePadraoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
