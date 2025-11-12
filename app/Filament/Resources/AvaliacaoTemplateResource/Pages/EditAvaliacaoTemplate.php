<?php

namespace App\Filament\Resources\AvaliacaoTemplateResource\Pages;

use App\Filament\Resources\AvaliacaoTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAvaliacaoTemplate extends EditRecord
{
    protected static string $resource = AvaliacaoTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
