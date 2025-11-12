<?php

namespace App\Filament\Resources\AvaliacaoResource\Pages;

use App\Filament\Resources\AvaliacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAvaliacao extends EditRecord
{
    protected static string $resource = AvaliacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
