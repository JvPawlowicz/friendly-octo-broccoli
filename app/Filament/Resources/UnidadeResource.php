<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnidadeResource\Pages;
use App\Filament\Resources\UnidadeResource\RelationManagers;
use App\Models\Unidade;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnidadeResource extends Resource
{
    protected static ?string $model = Unidade::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2'; // Ícone do menu
    protected static ?string $navigationGroup = 'Gestão do Sistema'; // Agrupa no menu
    protected static ?string $navigationLabel = 'Unidades';
    protected static ?string $modelLabel = 'Unidade';
    protected static ?string $pluralModelLabel = 'Unidades';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Secção Principal
                Section::make('Informações da Unidade')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nome')
                            ->label('Nome da Unidade')
                            ->required(),
                        
                        FileUpload::make('logo_unidade')
                            ->label('Logo da Unidade')
                            ->image() // Diz que é uma imagem
                            ->directory('logos-unidades') // Onde salvar (dentro de storage/app/public)
                            ->nullable(),
                    ]),

                // Secção de Endereço (com o CEP)
                Section::make('Endereço')
                    ->columns(4) // Layout em 4 colunas
                    ->schema([
                        TextInput::make('cep')
                            ->label('CEP')
                            ->maxLength(10)
                            ->columnSpan(1), // Ocupa 1 coluna

                        TextInput::make('logradouro')
                            ->columnSpan(3), // Ocupa 3 colunas
                        
                        TextInput::make('numero')
                            ->columnSpan(1),

                        TextInput::make('complemento')
                            ->columnSpan(1),
                            
                        TextInput::make('bairro')
                            ->columnSpan(2),

                        TextInput::make('cidade')
                            ->columnSpan(2),

                        TextInput::make('estado')
                            ->length(2) // Apenas 2 caracteres (ex: SP)
                            ->columnSpan(1),
                        
                        TextInput::make('telefone_principal')
                            ->label('Telefone')
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Mostra a imagem do logo na tabela
                ImageColumn::make('logo_unidade')
                    ->label('Logo'),

                TextColumn::make('nome')
                    ->label('Nome da Unidade')
                    ->searchable() // Permite procurar
                    ->sortable(), // Permite ordenar

                TextColumn::make('cidade')
                    ->sortable(),

                TextColumn::make('telefone_principal')
                    ->label('Telefone'),
            ])
            ->filters([
                // ...
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
            // Diz ao Filament para carregar o gestor de salas
            RelationManagers\SalasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnidades::route('/'),
            'create' => Pages\CreateUnidade::route('/create'),
            'edit' => Pages\EditUnidade::route('/{record}/edit'),
        ];
    }
}
