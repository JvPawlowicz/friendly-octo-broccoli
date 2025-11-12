<?php

namespace App\Filament\Resources\PacienteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
// Imports
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class ResponsaveisRelationManager extends RelationManager
{
    protected static string $relationship = 'responsaveis';

    protected static ?string $title = 'Responsáveis (Pais, Guardiões, etc.)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nome_completo')
                    ->label('Nome')
                    ->required()
                    ->columnSpan(2),
                
                TextInput::make('parentesco')
                    ->label('Parentesco (Mãe, Pai...)')
                    ->required()
                    ->columnSpan(1),

                TextInput::make('telefone_principal')
                    ->label('Telefone')
                    ->tel()
                    ->required()
                    ->columnSpan(1),
                
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->nullable()
                    ->columnSpan(1),
                
                TextInput::make('cpf')
                    ->label('CPF')
                    ->mask('999.999.999-99')
                    ->nullable()
                    ->columnSpan(1),
                
                // Checkboxes (Item 4.1)
                Checkbox::make('is_responsavel_legal')
                    ->label('Responsável Legal'),
                
                Checkbox::make('is_contato_emergencia')
                    ->label('Contato de Emergência'),
                
                Checkbox::make('recebe_comunicacoes')
                    ->label('Recebe Comunicações')
                    ->default(true),
            ])->columns(3); // Layout do formulário em 3 colunas
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome_completo')
            ->columns([
                TextColumn::make('nome_completo')
                    ->label('Nome')
                    ->searchable(),
                
                TextColumn::make('parentesco'),

                TextColumn::make('telefone_principal')
                    ->label('Telefone'),

                IconColumn::make('is_responsavel_legal')
                    ->label('Legal')
                    ->boolean(),
                
                IconColumn::make('is_contato_emergencia')
                    ->label('Emergência')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Botão "+ Novo Responsável"
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

