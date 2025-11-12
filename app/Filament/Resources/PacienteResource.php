<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacienteResource\Pages;
// Adicione a linha abaixo
use App\Filament\Resources\PacienteResource\RelationManagers;
use App\Models\Paciente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// --- Imports necessários ---
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
// -------------------------

class PacienteResource extends Resource
{
    protected static ?string $model = Paciente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pacientes';
    protected static ?string $modelLabel = 'Paciente';
    protected static ?string $pluralModelLabel = 'Pacientes';
    // Coloca o Paciente no topo do menu
    protected static ?int $navigationSort = -100; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Cadastro de Paciente')->tabs([
                    
                    // --- TAB 1: DADOS PRINCIPAIS ---
                    Tabs\Tab::make('Dados Principais')
                        ->schema([
                            Section::make('Identificação')
                                ->columns(3)
                                ->schema([
                                    FileUpload::make('foto_perfil')
                                        ->label('Foto')
                                        ->image()
                                        ->directory('avatars-pacientes')
                                        ->columnSpan(1),
                                    
                                    // Campos de nome
                                    Section::make()
                                        ->columnSpan(2)
                                        ->schema([
                                            TextInput::make('nome_completo')
                                                ->required(),
                                            TextInput::make('nome_social'),
                                        ]),

                                    TextInput::make('cpf')
                                        ->label('CPF')
                                        ->mask('999.999.999-99')
                                        ->columnSpan(1),

                                    DatePicker::make('data_nascimento')
                                        ->columnSpan(1),

                                    Select::make('status')
                                        ->options([
                                            'Ativo' => 'Ativo',
                                            'Inativo' => 'Inativo',
                                            'Em espera' => 'Em espera',
                                        ])
                                        ->default('Ativo')
                                        ->required()
                                        ->columnSpan(1),
                                ]),
                            
                            Section::make('Contato')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('email_principal')
                                        ->label('Email')
                                        ->email(),
                                    TextInput::make('telefone_principal')
                                        ->label('Telefone')
                                        ->tel(),
                                ]),
                            
                            Section::make('Endereço (Auto-preenchimento por CEP em breve)')
                                ->columns(4)
                                ->schema([
                                    TextInput::make('cep')->label('CEP')->columnSpan(1),
                                    TextInput::make('logradouro')->columnSpan(3),
                                    TextInput::make('numero')->columnSpan(1),
                                    TextInput::make('complemento')->columnSpan(1),
                                    TextInput::make('bairro')->columnSpan(2),
                                    TextInput::make('cidade')->columnSpan(2),
                                    TextInput::make('estado')->length(2)->columnSpan(1),
                                ]),
                        ]),
                    
                    // --- TAB 2: DADOS CLÍNICOS E PLANO ---
                    Tabs\Tab::make('Dados Clínicos e Convênio')
                        ->schema([
                            Section::make('Plano de Saúde (Convênio)')
                                ->columns(3)
                                ->schema([
                                    Select::make('plano_saude_id')
                                        ->label('Plano de Saúde')
                                        ->relationship('planoSaude', 'nome_plano') // Usa a relação
                                        ->preload()
                                        ->searchable()
                                        ->nullable(),
                                    
                                    TextInput::make('numero_carteirinha')
                                        ->label('Nº da Carteirinha'),
                                    
                                    DatePicker::make('validade_carteirinha')
                                        ->label('Validade'),
                                ]),
                            
                            Section::make('Dados Clínicos (Item 4.1)')
                                ->columns(1)
                                ->schema([
                                    Select::make('unidade_padrao_id')
                                        ->label('Unidade Padrão')
                                        ->relationship('unidadePadrao', 'nome')
                                        ->preload()
                                        ->searchable()
                                        ->helperText('A principal unidade que este paciente frequenta.'),
                                    
                                    Textarea::make('diagnostico_condicao')
                                        ->label('Diagnóstico / Condição'),
                                    Textarea::make('plano_de_crise')
                                        ->label('Plano de Crise'),
                                    Textarea::make('alergias_medicacoes')
                                        ->label('Alergias e Medicações'),
                                    TextInput::make('metodo_comunicacao')
                                        ->label('Método de Comunicação (Ex: Verbal, PECS)'),
                                    Textarea::make('informacoes_escola')
                                        ->label('Informações da Escola'),
                                    Textarea::make('informacoes_medicas_adicionais')
                                        ->label('Outras Informações Médicas'),
                                ]),
                        ]),

                ])->columnSpanFull(), // Faz as Tabs ocuparem a largura total
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // Se não for Admin, filtra apenas pacientes das unidades do usuário
                if (!auth()->user()->hasRole('Admin')) {
                    $unidadeIds = auth()->user()->unidades->pluck('id')->toArray();
                    $query->whereIn('unidade_padrao_id', $unidadeIds);
                }
            })
            ->columns([
                ImageColumn::make('foto_perfil')
                    ->label('Foto')
                    ->circular(), // Círculo

                TextColumn::make('nome_completo')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge() // Mostra como "tag"
                    ->color(fn (string $state): string => match ($state) {
                        'Ativo' => 'success',
                        'Inativo' => 'danger',
                        'Em espera' => 'warning',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('planoSaude.nome_plano') // Busca da relação
                    ->label('Plano de Saúde')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unidadePadrao.nome') // Busca da relação
                    ->label('Unidade')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Ativo' => 'Ativo',
                        'Inativo' => 'Inativo',
                        'Em espera' => 'Em espera',
                    ]),
                
                SelectFilter::make('unidadePadrao')
                    ->relationship('unidadePadrao', 'nome')
                    ->label('Unidade')
                    ->visible(fn () => auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Coordenador')),
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
    
    public static function canViewAny(): bool
    {
        // Apenas Admin, Coordenador e Secretaria podem ver pacientes
        return auth()->user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria']);
    }
    
    // Adicione este método (getRelations)
    public static function getRelations(): array
    {
        return [
            // Vamos carregar o gestor de Responsáveis
            RelationManagers\ResponsaveisRelationManager::class,
            // Vamos carregar o gestor de Documentos
            RelationManagers\DocumentosRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPacientes::route('/'),
            'create' => Pages\CreatePaciente::route('/create'),
            'edit' => Pages\EditPaciente::route('/{record}/edit'),
        ];
    }    
}
