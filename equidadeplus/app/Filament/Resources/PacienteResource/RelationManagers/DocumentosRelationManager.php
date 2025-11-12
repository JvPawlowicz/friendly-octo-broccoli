<?php

namespace App\Filament\Resources\PacienteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Auth;

class DocumentosRelationManager extends RelationManager
{
    protected static string $relationship = 'documentos';
    protected static ?string $title = 'Documentos do Paciente';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titulo_documento')
                    ->label('Título do Documento')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('path_arquivo')
                    ->label('Arquivo')
                    ->directory('documentos-pacientes')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(10240) // 10MB
                    ->required()
                    ->columnSpanFull(),

                Select::make('categoria')
                    ->label('Categoria')
                    ->options([
                        'Laudo' => 'Laudo',
                        'Exame' => 'Exame',
                        'Receita' => 'Receita',
                        'Atestado' => 'Atestado',
                        'Outro' => 'Outro',
                    ])
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo_documento')
            ->columns([
                TextColumn::make('titulo_documento')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->sortable(),

                TextColumn::make('usuario.name')
                    ->label('Upload por')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Data de Upload')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Preenche automaticamente o user_id com o usuário logado
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => asset('storage/' . $record->path_arquivo))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
