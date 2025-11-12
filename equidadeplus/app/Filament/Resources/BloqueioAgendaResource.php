<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BloqueioAgendaResource\Pages;
use App\Filament\Resources\BloqueioAgendaResource\RelationManagers;
use App\Models\BloqueioAgenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// --- Imports necessários ---
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
// -------------------------

class BloqueioAgendaResource extends Resource
{
    protected static ?string $model = BloqueioAgenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'Gestão Clínica'; // Mesmo grupo
    protected static ?string $navigationLabel = 'Bloqueios de Agenda';
    protected static ?string $modelLabel = 'Bloqueio de Agenda';
    protected static ?string $pluralModelLabel = 'Bloqueios de Agenda';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titulo_bloqueio')
                    ->label('Título (Ex: Feriado, Férias Dr. João, Manutenção Sala 1)')
                    ->required()
                    ->columnSpanFull(),
                
                DateTimePicker::make('data_hora_inicio')
                    ->label('Início')
                    ->seconds(false) // Sem segundos
                    ->required(),

                DateTimePicker::make('data_hora_fim')
                    ->label('Fim')
                    ->seconds(false)
                    ->required(),
                
                Section::make('Tipo de Bloqueio (Opcional)')
                    ->description('Deixe todos em branco para um bloqueio global (ex: Feriado).')
                    ->columns(3)
                    ->schema([
                        Select::make('unidade_id')
                            ->label('Bloquear Unidade Inteira')
                            ->relationship('unidade', 'nome')
                            ->searchable()
                            ->preload(),
                        
                        Select::make('user_id')
                            ->label('Bloquear Profissional (Férias)')
                            ->relationship('profissional', 'name')
                            ->searchable()
                            ->preload(),
                        
                        Select::make('sala_id')
                            ->label('Bloquear Sala (Manutenção)')
                            ->relationship('sala', 'nome')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo_bloqueio')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('data_hora_inicio')
                    ->label('Início')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('data_hora_fim')
                    ->label('Fim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                // Mostra o tipo de bloqueio
                TextColumn::make('profissional.name')->label('Profissional'),
                TextColumn::make('unidade.nome')->label('Unidade'),
                TextColumn::make('sala.nome')->label('Sala'),
            ])
            ->filters([
                // Filtro para ver apenas bloqueios futuros
                Filter::make('futuros')
                    ->label('Apenas Bloqueios Futuros')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('data_hora_inicio', '>=', now())
                    )
                    ->default(), // Vem marcado por defeito
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
            ->defaultSort('data_hora_inicio', 'asc'); // Mais próximos primeiro
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
            'index' => Pages\ListBloqueioAgendas::route('/'),
            'create' => Pages\CreateBloqueioAgenda::route('/create'),
            'edit' => Pages\EditBloqueioAgenda::route('/{record}/edit'),
        ];
    }    
}
