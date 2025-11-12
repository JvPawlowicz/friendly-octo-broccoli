<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AvaliacaoResource\Pages;
// Adicione esta linha
use App\Filament\Resources\AvaliacaoResource\RelationManagers; 
use App\Models\Avaliacao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// --- Imports necessários ---
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
// -------------------------

class AvaliacaoResource extends Resource
{
    protected static ?string $model = Avaliacao::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Gestão Clínica'; // Mesmo grupo do Prompt 5
    protected static ?string $navigationLabel = 'Avaliações Aplicadas';
    protected static ?string $modelLabel = 'Avaliação Aplicada';
    protected static ?string $pluralModelLabel = 'Avaliações Aplicadas';

    /**
     * Remove o botão "Nova Avaliação" do Superadmin.
     * A criação será feita pelo profissional no seu painel.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Este formulário será usado para VER (e talvez editar o status),
     * mas não para preencher as respostas (por enquanto).
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cabeçalho da Avaliação')
                    ->columns(3)
                    ->schema([
                        // Mostra o Paciente (read-only)
                        Select::make('paciente_id')
                            ->relationship('paciente', 'nome_completo')
                            ->label('Paciente')
                            ->disabled(),
                        
                        // Mostra o Profissional (read-only)
                        Select::make('user_id')
                            ->relationship('profissional', 'name')
                            ->label('Profissional')
                            ->disabled(),
                        
                        // Mostra o Template (read-only)
                        Select::make('avaliacao_template_id')
                            ->relationship('template', 'nome_template')
                            ->label('Modelo Utilizado')
                            ->disabled(),

                        // O único campo editável pelo Admin
                        Select::make('status')
                            ->options([
                                'Rascunho' => 'Rascunho',
                                'Finalizado' => 'Finalizado',
                            ])
                            ->required(),
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
                
                TextColumn::make('template.nome_template')
                    ->label('Modelo')
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
                
                TextColumn::make('created_at')
                    ->label('Data da Aplicação')
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
                Tables\Actions\EditAction::make(), // Editar (para mudar o status)
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
            // Vamos carregar o gestor de Respostas
            RelationManagers\RespostasRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAvaliacaos::route('/'),
            // Removemos a página de Criação
            // 'create' => Pages\CreateAvaliacao::route('/create'), 
            'view' => Pages\ViewAvaliacao::route('/{record}'), // Adiciona página de "Ver"
            'edit' => Pages\EditAvaliacao::route('/{record}/edit'),
        ];
    }    
}
