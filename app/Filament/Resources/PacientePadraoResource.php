<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacientePadraoResource\Pages;
use App\Models\Paciente;
use App\Models\Unidade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PacientePadraoResource extends Resource
{
    protected static ?string $model = Paciente::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Gestão do Sistema';
    protected static ?string $navigationLabel = 'Pacientes Padrão';
    protected static ?string $modelLabel = 'Paciente Padrão';
    protected static ?string $pluralModelLabel = 'Pacientes Padrão';

    /**
     * Filtra apenas pacientes padrão (cpf = '00000000000')
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('cpf', '00000000000');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações Básicas')
                    ->schema([
                        TextInput::make('nome_completo')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ex: Horário Vago, Reunião, Capacitação, etc.')
                            ->placeholder('Ex: Horário Vago / Reunião'),
                        
                        TextInput::make('nome_social')
                            ->label('Nome Alternativo')
                            ->maxLength(255)
                            ->helperText('Nome curto que aparecerá na agenda (opcional)')
                            ->placeholder('Ex: Horário Vago'),
                        
                        Textarea::make('descricao_uso')
                            ->label('Descrição de Uso')
                            ->rows(3)
                            ->helperText('Descreva quando e como este paciente padrão deve ser usado')
                            ->placeholder('Ex: Use este paciente padrão para bloquear horários vagos, agendar reuniões internas, etc.'),
                    ])
                    ->columns(1),

                Section::make('Configurações de Agendamento')
                    ->schema([
                        Toggle::make('contar_como_atendimento')
                            ->label('Contar como Atendimento')
                            ->helperText('Se ativado, os agendamentos com este paciente padrão serão contabilizados nas estatísticas de atendimentos')
                            ->default(true)
                            ->required(),

                        Select::make('unidade_padrao_id')
                            ->label('Unidade Padrão')
                            ->relationship('unidadePadrao', 'nome')
                            ->searchable()
                            ->preload()
                            ->helperText('Unidade principal associada a este paciente padrão'),

                        CheckboxList::make('unidades_permitidas')
                            ->label('Unidades Permitidas')
                            ->options(function () {
                                return Unidade::orderBy('nome')->pluck('nome', 'id')->toArray();
                            })
                            ->helperText('Selecione as unidades onde este paciente padrão pode ser usado. Deixe vazio para permitir em todas.')
                            ->descriptions(function () {
                                return Unidade::orderBy('nome')->get()->mapWithKeys(fn($u) => [
                                    $u->id => ($u->cidade ?? '') . ', ' . ($u->estado ?? '')
                                ])->toArray();
                            })
                            ->columns(2)
                            ->searchable(),

                        CheckboxList::make('tipos_agenda_permitidos')
                            ->label('Tipos de Agenda Permitidos')
                            ->options([
                                'horario_vago' => 'Horário Vago',
                                'reuniao' => 'Reunião',
                                'capacitacao' => 'Capacitação',
                                'ferias' => 'Férias',
                                'licenca' => 'Licença',
                                'outro' => 'Outro',
                            ])
                            ->helperText('Selecione os tipos de agenda onde este paciente padrão pode ser usado. Deixe vazio para permitir em todos.')
                            ->columns(2),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome_completo')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nome_social')
                    ->label('Nome Alternativo')
                    ->searchable(),

                IconColumn::make('contar_como_atendimento')
                    ->label('Conta como Atendimento')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('unidadePadrao.nome')
                    ->label('Unidade Padrão')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('unidades_permitidas')
                    ->label('Unidades Permitidas')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'Todas';
                        }
                        $unidades = Unidade::whereIn('id', $state)->pluck('nome')->toArray();
                        return count($unidades) > 2 
                            ? implode(', ', array_slice($unidades, 0, 2)) . ' +' . (count($unidades) - 2) . ' mais'
                            : implode(', ', $unidades);
                    })
                    ->wrap(),

                TextColumn::make('tipos_agenda_permitidos')
                    ->label('Tipos Permitidos')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'Todos';
                        }
                        $tipos = [
                            'horario_vago' => 'Horário Vago',
                            'reuniao' => 'Reunião',
                            'capacitacao' => 'Capacitação',
                            'ferias' => 'Férias',
                            'licenca' => 'Licença',
                            'outro' => 'Outro',
                        ];
                        $labels = array_map(fn($t) => $tipos[$t] ?? $t, $state);
                        return count($labels) > 2 
                            ? implode(', ', array_slice($labels, 0, 2)) . ' +' . (count($labels) - 2) . ' mais'
                            : implode(', ', $labels);
                    })
                    ->wrap(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ativo' => 'success',
                        'Inativo' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('contar_como_atendimento')
                    ->label('Conta como Atendimento')
                    ->options([
                        true => 'Sim',
                        false => 'Não',
                    ]),

                SelectFilter::make('unidade_padrao_id')
                    ->label('Unidade Padrão')
                    ->relationship('unidadePadrao', 'nome'),

                SelectFilter::make('status')
                    ->options([
                        'Ativo' => 'Ativo',
                        'Inativo' => 'Inativo',
                    ]),
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
            'index' => Pages\ListPacientePadraos::route('/'),
            'create' => Pages\CreatePacientePadrao::route('/create'),
            'edit' => Pages\EditPacientePadrao::route('/{record}/edit'),
        ];
    }

    /**
     * Impede criação de múltiplos pacientes padrão com mesmo CPF
     */
    public static function canCreate(): bool
    {
        // Permite criar, mas o CPF será sempre '00000000000'
        // O sistema pode ter múltiplos pacientes padrão com diferentes configurações
        return true;
    }
}
