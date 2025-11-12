<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
// Imports
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;

class DisponibilidadesRelationManager extends RelationManager
{
    protected static string $relationship = 'disponibilidades';

    protected static ?string $title = 'Horários de Disponibilidade';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('dia_da_semana')
                    ->label('Dia da Semana')
                    ->options([
                        // 0=Dom, 1=Seg, 2=Ter...
                        1 => 'Segunda-feira',
                        2 => 'Terça-feira',
                        3 => 'Quarta-feira',
                        4 => 'Quinta-feira',
                        5 => 'Sexta-feira',
                        6 => 'Sábado',
                        0 => 'Domingo',
                    ])
                    ->required(),
                
                TimePicker::make('hora_inicio')
                    ->label('Início')
                    ->seconds(false) // Não mostra os segundos
                    ->required(),
                
                TimePicker::make('hora_fim')
                    ->label('Fim')
                    ->seconds(false)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dia_da_semana')
                    ->label('Dia da Semana')
                    // Formata o número (0-6) para o nome
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Domingo',
                        1 => 'Segunda-feira',
                        2 => 'Terça-feira',
                        3 => 'Quarta-feira',
                        4 => 'Quinta-feira',
                        5 => 'Sexta-feira',
                        6 => 'Sábado',
                        default => 'N/A',
                    })
                    ->sortable(),
                
                TextColumn::make('hora_inicio')
                    ->time('H:i') // Formato 24h
                    ->sortable(),

                TextColumn::make('hora_fim')
                    ->time('H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Botão "+ Novo Horário"
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

