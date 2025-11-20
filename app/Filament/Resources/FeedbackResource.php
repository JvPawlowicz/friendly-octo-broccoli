<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Filament\Resources\FeedbackResource\RelationManagers;
use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\Builder;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Gestão do Sistema';
    protected static ?string $navigationLabel = 'Feedbacks';
    protected static ?string $modelLabel = 'Feedback';
    protected static ?string $pluralModelLabel = 'Feedbacks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Feedback')
                    ->schema([
                        TextInput::make('user.name')
                            ->label('Usuário')
                            ->disabled()
                            ->dehydrated(false),
                        
                        TextInput::make('assunto')
                            ->label('Assunto')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('mensagem')
                            ->label('Mensagem')
                            ->required()
                            ->rows(6)
                            ->maxLength(2000),
                    ])
                    ->columns(1),

                Section::make('Resposta e Gerenciamento')
                    ->description('Preencha a resposta para o usuário e atualize o status conforme necessário.')
                    ->schema([
                        Textarea::make('resposta')
                            ->label('Sua Resposta')
                            ->rows(6)
                            ->placeholder('Digite sua resposta ao feedback do usuário...')
                            ->maxLength(2000)
                            ->helperText('Esta resposta será registrada e associada ao seu usuário.')
                            ->columnSpanFull(),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pendente' => 'Pendente',
                                'em_andamento' => 'Em Andamento',
                                'resolvido' => 'Resolvido',
                                'fechado' => 'Fechado',
                            ])
                            ->required()
                            ->default('pendente')
                            ->helperText('O status será automaticamente atualizado para "Em Andamento" quando você adicionar uma resposta.'),
                        
                        DateTimePicker::make('respondido_em')
                            ->label('Data da Resposta')
                            ->default(now())
                            ->disabled()
                            ->visible(fn ($get, $record) => !empty($get('resposta')) || ($record && $record->respondido_em)),
                        
                        TextInput::make('respondidoPor.name')
                            ->label('Respondido por')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($get, $record) => !empty($get('resposta')) || ($record && $record->respondido_por)),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('assunto')
                    ->label('Assunto')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (Feedback $record): string => $record->assunto),
                
                TextColumn::make('mensagem')
                    ->label('Mensagem')
                    ->limit(30)
                    ->tooltip(fn (Feedback $record): string => $record->mensagem)
                    ->wrap(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pendente' => 'warning',
                        'em_andamento' => 'info',
                        'resolvido' => 'success',
                        'fechado' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pendente' => 'Pendente',
                        'em_andamento' => 'Em Andamento',
                        'resolvido' => 'Resolvido',
                        'fechado' => 'Fechado',
                        default => $state,
                    }),
                
                TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('respondido_em')
                    ->label('Respondido em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—'),
                
                TextColumn::make('respondidoPor.name')
                    ->label('Respondido por')
                    ->placeholder('—'),
                
                TextColumn::make('resposta')
                    ->label('Resposta')
                    ->limit(30)
                    ->tooltip(fn (Feedback $record): string => $record->resposta ?? 'Sem resposta')
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pendente' => 'Pendente',
                        'em_andamento' => 'Em Andamento',
                        'resolvido' => 'Resolvido',
                        'fechado' => 'Fechado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Responder/Editar'),
                Tables\Actions\Action::make('responder_rapido')
                    ->label('Responder Rápido')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->visible(fn (Feedback $record) => empty($record->resposta))
                    ->form([
                        Textarea::make('resposta')
                            ->label('Resposta')
                            ->required()
                            ->rows(4)
                            ->placeholder('Digite sua resposta...')
                            ->maxLength(2000),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'em_andamento' => 'Em Andamento',
                                'resolvido' => 'Resolvido',
                            ])
                            ->default('em_andamento')
                            ->required(),
                    ])
                    ->action(function (Feedback $record, array $data) {
                        $record->update([
                            'resposta' => $data['resposta'],
                            'status' => $data['status'],
                            'respondido_por' => auth()->id(),
                            'respondido_em' => now(),
                        ]);
                    })
                    ->successNotificationTitle('Resposta enviada com sucesso!'),
                Tables\Actions\Action::make('marcar_resolvido')
                    ->label('Marcar como Resolvido')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Feedback $record) => $record->status !== 'resolvido' && !empty($record->resposta))
                    ->action(function (Feedback $record) {
                        $record->update([
                            'status' => 'resolvido',
                        ]);
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListFeedback::route('/'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Feedbacks são criados apenas pelo frontend
    }
}
