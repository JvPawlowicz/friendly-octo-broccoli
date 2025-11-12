<?php

namespace App\Filament\Resources\AvaliacaoTemplateResource\Pages;

use App\Filament\Resources\AvaliacaoTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAvaliacaoTemplates extends ListRecords
{
    protected static string $resource = AvaliacaoTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
