<?php

namespace App\Filament\Resources\AvaliacaoResource\Pages;

use App\Filament\Resources\AvaliacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAvaliacaos extends ListRecords
{
    protected static string $resource = AvaliacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
