<?php

namespace App\Filament\Resources\EvolucaoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
// Imports
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class AdendosRelationManager extends RelationManager
{
    protected static string $relationship = 'adendos';
    protected static ?string $title = 'Adendos desta Evolução';

    /**
     * Remove todos os botões de Ação
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
                TextColumn::make('profissional.name')
                    ->label('Autor'),
                
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i'),
                
                TextColumn::make('relato_clinico')
                    ->label('Relato')
                    ->wrap()
                    ->limit(100), // Limita o texto na tabela
            ])
            // Remove ações
            ->headerActions([]) 
            ->actions([
                // Talvez um botão para "Ver" o adendo completo
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}

