<?php

namespace App\Filament\Resources\FeedbackResource\Pages;

use App\Filament\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeedback extends EditRecord
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Se há resposta e ainda não foi respondido, marca como respondido
        if (!empty($data['resposta']) && empty($this->record->respondido_em)) {
            $data['respondido_por'] = auth()->id();
            $data['respondido_em'] = now();
            
            // Se estava pendente e não foi especificado outro status, muda para em_andamento
            if ($this->record->status === 'pendente' && (!isset($data['status']) || $data['status'] === 'pendente')) {
                $data['status'] = 'em_andamento';
            }
        }
        
        // Se está removendo a resposta, limpa os campos de resposta
        if (empty($data['resposta']) && $this->record->respondido_em) {
            $data['respondido_por'] = null;
            $data['respondido_em'] = null;
        }
        
        return $data;
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Feedback atualizado com sucesso!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
