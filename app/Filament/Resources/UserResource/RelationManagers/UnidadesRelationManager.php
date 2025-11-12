<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
// Imports
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\AttachAction; // Usamos Attach
use Filament\Tables\Actions\DetachAction;

class UnidadesRelationManager extends RelationManager
{
    protected static string $relationship = 'unidades';

    protected static ?string $title = 'Unidades Associadas';

    public function form(Form $form): Form
    {
        // Este formulário não será usado para criar/editar,
        // apenas para associar, por isso pode ficar simples.
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome')
            ->columns([
                TextColumn::make('nome'),
                TextColumn::make('cidade'),
            ])
            ->headerActions([
                // Botão "+ Associar Unidade"
                AttachAction::make() 
                    ->preloadRecordSelect(), // Permite procurar as unidades
            ])
            ->actions([
                // Botão "Desassociar"
                DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}

