<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvolucaoResource\Pages;
// Adicione esta linha
use App\Filament\Resources\EvolucaoResource\RelationManagers;
use App\Models\Evolucao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// --- Imports necessários ---
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DateTimePicker;
// -------------------------

class EvolucaoResource extends Resource
{
    protected static ?string $model = Evolucao::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Gestão Clínica'; // Mesmo grupo
    protected static ?string $navigationLabel = 'Evoluções Clínicas';
    protected static ?string $modelLabel = 'Evolução';
    protected static ?string $pluralModelLabel = 'Evoluções';

    /**
     * Remove o botão "Nova Evolução" do Superadmin.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Formulário para VER os detalhes da evolução
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cabeçalho')
                    ->columns(3)
                    ->schema([
                        Select::make('paciente_id')
                            ->relationship('paciente', 'nome_completo')
                            ->label('Paciente')
                            ->disabled(),
                        
                        Select::make('user_id')
                            ->relationship('profissional', 'name')
                            ->label('Profissional')
                            ->disabled(),
                        
                        Select::make('status')
                            ->options([
                                'Rascunho' => 'Rascunho',
                                'Finalizado' => 'Finalizado',
                            ])
                            ->disabled(), // Admin não deve mudar o status clínico
                    ]),
                
                Section::make('Registro Clínico (Item 5.2)')
                    ->schema([
                        Textarea::make('relato_clinico')
                            ->label('Relato Clínico')
                            ->rows(5)
                            ->disabled(),
                        
                        Textarea::make('conduta')
                            ->label('Conduta')
                            ->rows(5)
                            ->disabled(),
                        
                        Textarea::make('objetivos')
                            ->label('Objetivos')
                            ->rows(5)
                            ->disabled(),
                    ]),
                
                Section::make('Metadados')
                    ->columns(2)
                    ->schema([
                        // Se for um adendo, mostra o link para o pai
                        Select::make('evolucao_pai_id')
                            ->relationship('evolucaoPai', 'id')
                            ->label('Adendo da Evolução #')
                            ->disabled(),

                        DateTimePicker::make('finalizado_em')
                            ->label('Finalizado em')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paciente.nome_completo')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('profissional.name')
                    ->label('Profissional')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Rascunho' => 'warning',
                        'Finalizado' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                
                // Coluna para identificar se é um Adendo
                TextColumn::make('evolucao_pai_id')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state): string => 
                        $state ? 'Adendo' : 'Evolução'
                    )
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Rascunho' => 'Rascunho',
                        'Finalizado' => 'Finalizado',
                    ]),
                SelectFilter::make('paciente')
                    ->relationship('paciente', 'nome_completo')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Apenas Ver
                Tables\Actions\DeleteAction::make(), // Admin pode apagar rascunhos
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc'); // Mais recentes primeiro
    }
    
    // Adicione este método (getRelations)
    public static function getRelations(): array
    {
        return [
            // Vamos carregar o gestor de Adendos
            RelationManagers\AdendosRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvolucaos::route('/'),
            'view' => Pages\ViewEvolucao::route('/{record}'), // Adiciona página de "Ver"
        ];
    }    
}
