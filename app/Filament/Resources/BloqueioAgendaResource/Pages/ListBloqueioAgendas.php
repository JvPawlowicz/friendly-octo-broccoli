<?php

namespace App\Filament\Resources\BloqueioAgendaResource\Pages;

use App\Filament\Resources\BloqueioAgendaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBloqueioAgendas extends ListRecords
{
    protected static string $resource = BloqueioAgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
