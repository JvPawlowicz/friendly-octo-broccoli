<?php

namespace App\Filament\Resources\AvaliacaoTemplateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
// Imports
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Hidden;

class PerguntasRelationManager extends RelationManager
{
    protected static string $relationship = 'perguntas';
    protected static ?string $title = 'Perguntas do Modelo';

    // Permite reordenar as perguntas
    protected static ?string $reorderable = 'ordem'; 

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titulo_pergunta')
                    ->label('Texto da Pergunta / Título da Seção')
                    ->required()
                    ->columnSpanFull(),
                
                Select::make('tipo_campo')
                    ->label('Tipo de Campo de Resposta')
                    ->options([
                        // A nossa "Opção A" Simplificada
                        'texto_curto' => 'Texto Curto (1 linha)',
                        'texto_longo' => 'Texto Longo (parágrafo)',
                        'data' => 'Data',
                        'sim_nao' => 'Sim / Não (checkbox)',
                    ])
                    ->required(),
                
                // Campo invisível para guardar a ordem
                Hidden::make('ordem')
                    ->default(fn ($livewire) => $livewire->ownerRecord->perguntas()->count() + 1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo_pergunta')
            ->columns([
                TextColumn::make('ordem')
                    ->label('#')
                    ->sortable(),
                
                TextColumn::make('titulo_pergunta')
                    ->label('Pergunta'),
                
                TextColumn::make('tipo_campo')
                    ->label('Tipo de Campo')
                    // Formata para ficar mais amigável
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'texto_curto' => 'Texto Curto',
                        'texto_longo' => 'Texto Longo',
                        'data' => 'Data',
                        'sim_nao' => 'Sim / Não',
                        default => $state,
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Botão "+ Nova Pergunta"
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // Ordena pela coluna 'ordem' por defeito
            ->defaultSort('ordem', 'asc');
    }
}

