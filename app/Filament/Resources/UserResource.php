<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
// Adicione esta linha
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// --- Imports necessários ---
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // Importa o modelo Role
// -------------------------

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gestão do Sistema'; // Agrupa no menu
    protected static ?string $navigationLabel = 'Utilizadores';
    protected static ?string $modelLabel = 'Utilizador';
    protected static ?string $pluralModelLabel = 'Utilizadores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identificação e Acesso')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('foto_perfil')
                            ->label('Foto')
                            ->image()
                            ->directory('avatars')
                            ->columnSpan(1),
                        
                        // Este 'placeholder' é só para ocupar espaço
                        Forms\Components\Placeholder::make('espaco') 
                            ->columnSpan(1),

                        TextInput::make('name')
                            ->label('Nome Completo')
                            ->required(),

                        TextInput::make('email')
                            ->email()
                            ->required(),
                        
                        // Campo para definir o Papel (Role)
                        Select::make('roles')
                            ->label('Papel / Perfil')
                            ->relationship('roles', 'name') // Usa a relação do Spatie
                            ->multiple() // Um utilizador pode ter múltiplos papéis
                            ->preload() // Carrega os papéis (Admin, Coordenador...)
                            ->required(),

                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            // Ao criar, é obrigatório. Ao editar, é opcional
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)) // Encripta a senha
                            ->dehydrated(fn (?string $state): bool => filled($state)) // Só atualiza se for preenchido
                            ->maxLength(255),
                    ]),
                
                Section::make('Dados Profissionais')
                    ->columns(3)
                    ->schema([
                        TextInput::make('cargo')
                            ->label('Cargo (Ex: Fisioterapeuta)')
                            ->columnSpan(1),
                        
                        TextInput::make('conselho_profissional')
                            ->label('Conselho (Ex: CREFITO, CRP)')
                            ->columnSpan(1),

                        TextInput::make('numero_conselho')
                            ->label('Nº do Conselho')
                            ->columnSpan(1),

                        Textarea::make('especialidades')
                            ->label('Especialidades')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_perfil')
                    ->label('Foto')
                    ->circular(), // Mostra como círculo

                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),

                TextColumn::make('cargo')
                    ->searchable(),

                // Mostra os papéis do utilizador
                TextColumn::make('roles.name') 
                    ->label('Papel')
                    ->badge() // Mostra como uma "tag"
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),
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
            // Vamos adicionar os gestores de Unidades e Disponibilidade
            RelationManagers\UnidadesRelationManager::class,
            RelationManagers\DisponibilidadesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}
