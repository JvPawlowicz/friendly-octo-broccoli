<?php

namespace App\Filament\Resources\BloqueioAgendaResource\Pages;

use App\Filament\Resources\BloqueioAgendaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBloqueioAgenda extends EditRecord
{
    protected static string $resource = BloqueioAgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
