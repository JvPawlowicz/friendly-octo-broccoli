<?php

namespace App\Filament\Resources\EvolucaoResource\Pages;

use App\Filament\Resources\EvolucaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvolucaos extends ListRecords
{
    protected static string $resource = EvolucaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
