<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanoSaudeResource\Pages;
use App\Filament\Resources\PlanoSaudeResource\RelationManagers;
use App\Models\PlanoSaude;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// --- Imports necessários ---
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
// -------------------------

class PlanoSaudeResource extends Resource
{
    protected static ?string $model = PlanoSaude::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Gestão do Sistema'; // Agrupa no menu
    protected static ?string $navigationLabel = 'Planos de Saúde';
    protected static ?string $modelLabel = 'Plano de Saúde';
    protected static ?string $pluralModelLabel = 'Planos de Saúde';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('nome_plano')
                            ->label('Nome do Plano')
                            ->required(),
                        
                        TextInput::make('codigo_ans')
                            ->label('Código ANS')
                            ->nullable(),

                        Toggle::make('status')
                            ->label('Plano Ativo')
                            ->default(true) // Por defeito, vem marcado
                            ->helperText('Planos inativos não aparecerão para novos pacientes.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome_plano')
                    ->label('Nome do Plano')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('codigo_ans')
                    ->label('Código ANS')
                    ->searchable(),

                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean() // Mostra ícone de 'check' ou 'x'
                    ->sortable(),
            ])
            ->filters([
                // Filtro para ver Ativos / Inativos
                TernaryFilter::make('status')
                    ->label('Status')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos')
                    ->placeholder('Todos'),
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
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanoSaudes::route('/'),
            'create' => Pages\CreatePlanoSaude::route('/create'),
            'edit' => Pages\EditPlanoSaude::route('/{record}/edit'),
        ];
    }    
}
