<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AvaliacaoTemplateResource\Pages;
// Adicione a linha abaixo
use App\Filament\Resources\AvaliacaoTemplateResource\RelationManagers;
use App\Models\AvaliacaoTemplate;
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
// -------------------------

class AvaliacaoTemplateResource extends Resource
{
    protected static ?string $model = AvaliacaoTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Gestão Clínica'; // Novo grupo de menu
    protected static ?string $navigationLabel = 'Modelos de Avaliação';
    protected static ?string $modelLabel = 'Modelo de Avaliação';
    protected static ?string $pluralModelLabel = 'Modelos de Avaliação';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('nome_template')
                            ->label('Nome do Modelo (Ex: Anamnese, Avaliação Periódica)')
                            ->required(),
                        
                        Toggle::make('status')
                            ->label('Modelo Ativo')
                            ->default(true)
                            ->helperText('Modelos inativos não aparecerão para os profissionais.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome_template')
                    ->label('Nome do Modelo')
                    ->searchable()
                    ->sortable(),
                
                // Mostra quantas perguntas o modelo tem
                TextColumn::make('perguntas_count')
                    ->counts('perguntas') // Conta a relação 'perguntas'
                    ->label('Nº de Perguntas')
                    ->sortable(),

                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                //
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
    
    // Adicione este método (getRelations)
    public static function getRelations(): array
    {
        return [
            // Vamos carregar o gestor de Perguntas
            RelationManagers\PerguntasRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAvaliacaoTemplates::route('/'),
            'create' => Pages\CreateAvaliacaoTemplate::route('/create'),
            'edit' => Pages\EditAvaliacaoTemplate::route('/{record}/edit'),
        ];
    }    
}
