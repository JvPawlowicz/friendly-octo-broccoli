<?php

namespace App\Filament\Resources\AvaliacaoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
// Imports
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class RespostasRelationManager extends RelationManager
{
    protected static string $relationship = 'respostas';
    protected static ?string $title = 'Respostas da Avaliação';

    /**
     * Remove todos os botões de Ação (Criar, Editar, Apagar)
     * pois as respostas são geridas pelo profissional.
     */
    public function canCreate(): bool { return false; }
    public function canEdit(Model $record): bool { return false; }
    public function canDelete(Model $record): bool { return false; }
    public function canDeleteAny(): bool { return false; }


    public function form(Form $form): Form
    {
        // Formulário não será usado
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pergunta.titulo_pergunta')
                    ->label('Pergunta')
                    ->wrap(), // Quebra a linha se o texto for longo
                
                TextColumn::make('resposta')
                    ->label('Resposta')
                    ->wrap(),
            ])
            // Remove ações
            ->headerActions([]) 
            ->actions([])
            ->bulkActions([]);
    }
}

