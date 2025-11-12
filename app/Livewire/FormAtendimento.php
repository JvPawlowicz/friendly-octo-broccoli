<?php

namespace App\Livewire;

use App\Models\Atendimento;
use App\Models\BloqueioAgenda;
use App\Models\Paciente;
use App\Models\Sala;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class FormAtendimento extends Component
{
    public ?int $atendimentoId = null;
    public ?int $pacienteId = null;
    public ?int $userId = null;
    public ?int $salaId = null;
    public string $status = 'Agendado';
    public string $dataHoraInicio = '';
    public string $dataHoraFim = '';
    
    // Recorrência
    public bool $repetir = false;
    public string $tipoRecorrencia = 'semanal'; // semanal, quinzenal, mensal
    public int $vezesRepetir = 4;

    public $conflitos = [];
    public $erros = [];

    public function mount(?int $atendimentoId = null, ?string $dataInicio = null, ?int $pacienteId = null, ?int $userId = null, ?int $salaId = null)
    {
        $this->atendimentoId = $atendimentoId;
        $this->pacienteId = $pacienteId;
        $this->userId = $userId;
        $this->salaId = $salaId;
        
        if ($atendimentoId) {
            $atendimento = Atendimento::findOrFail($atendimentoId);
            Gate::authorize('update', $atendimento);
            $this->pacienteId = $atendimento->paciente_id;
            $this->userId = $atendimento->user_id;
            $this->salaId = $atendimento->sala_id;
            $this->status = $atendimento->status;
            $this->dataHoraInicio = $atendimento->data_hora_inicio->format('Y-m-d\TH:i');
            $this->dataHoraFim = $atendimento->data_hora_fim->format('Y-m-d\TH:i');
        } elseif ($dataInicio) {
            $this->dataHoraInicio = Carbon::parse($dataInicio)->format('Y-m-d\TH:i');
            $this->dataHoraFim = Carbon::parse($dataInicio)->addHour()->format('Y-m-d\TH:i');
        }
    }

    /**
     * Verifica conflitos para uma data específica (usado na recorrência)
     */
    private function verificarConflitosParaData(Carbon $inicio, Carbon $fim): array
    {
        $conflitos = [];

        // Verifica conflitos com outros atendimentos
        $query = Atendimento::where('id', '!=', $this->atendimentoId ?? 0)
            ->where(function ($q) use ($inicio, $fim) {
                $q->whereBetween('data_hora_inicio', [$inicio, $fim])
                  ->orWhereBetween('data_hora_fim', [$inicio, $fim])
                  ->orWhere(function ($q2) use ($inicio, $fim) {
                      $q2->where('data_hora_inicio', '<=', $inicio)
                         ->where('data_hora_fim', '>=', $fim);
                  });
            })
            ->where('status', '!=', 'Cancelado');

        // Conflito com profissional
        if ($this->userId) {
            $conflitoProfissional = (clone $query)->where('user_id', $this->userId)->first();
            if ($conflitoProfissional) {
                $conflitos[] = "Profissional já tem atendimento agendado: {$conflitoProfissional->paciente->nome_completo}";
            }
        }

        // Conflito com sala
        if ($this->salaId) {
            $conflitoSala = (clone $query)->where('sala_id', $this->salaId)->first();
            if ($conflitoSala) {
                $conflitos[] = "Sala já está ocupada: {$conflitoSala->paciente->nome_completo}";
            }
        }

        // Conflito com paciente
        if ($this->pacienteId) {
            $conflitoPaciente = (clone $query)->where('paciente_id', $this->pacienteId)->first();
            if ($conflitoPaciente) {
                $conflitos[] = "Paciente já tem atendimento agendado neste horário";
            }
        }

        // Verifica bloqueios
        $bloqueios = BloqueioAgenda::where(function ($q) use ($inicio, $fim) {
            $q->whereBetween('data_hora_inicio', [$inicio, $fim])
              ->orWhereBetween('data_hora_fim', [$inicio, $fim])
              ->orWhere(function ($q2) use ($inicio, $fim) {
                  $q2->where('data_hora_inicio', '<=', $inicio)
                     ->where('data_hora_fim', '>=', $fim);
              });
        });

        if ($this->userId) {
            $bloqueios->where(function ($q) {
                $q->where('user_id', $this->userId)->orWhereNull('user_id');
            });
        }

        if ($this->salaId) {
            $bloqueios->where(function ($q) {
                $q->where('sala_id', $this->salaId)->orWhereNull('sala_id');
            });
        }

        $bloqueio = $bloqueios->first();
        if ($bloqueio) {
            $conflitos[] = "Horário bloqueado: {$bloqueio->titulo_bloqueio}";
        }

        return $conflitos;
    }

    public function verificarConflitos()
    {
        $this->conflitos = [];
        $this->erros = [];

        if (!$this->dataHoraInicio || !$this->dataHoraFim) {
            return;
        }

        $inicio = Carbon::parse($this->dataHoraInicio);
        $fim = Carbon::parse($this->dataHoraFim);
        
        $conflitosEncontrados = $this->verificarConflitosParaData($inicio, $fim);
        
        foreach ($conflitosEncontrados as $conflito) {
            $this->conflitos[] = [
                'tipo' => 'geral',
                'mensagem' => $conflito,
            ];
        }

        // Validações básicas
        if ($fim <= $inicio) {
            $this->erros[] = 'A data/hora de fim deve ser posterior à de início.';
            return;
        }

    }

    public function salvar()
    {
        // Verifica permissão - Admin tem acesso total
        if (!Auth::user()->can('editar_agenda_unidade') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            session()->flash('error', 'Você não tem permissão para criar ou editar atendimentos.');
            return;
        }

        $atendimentoExistente = null;
        if ($this->atendimentoId) {
            $atendimentoExistente = Atendimento::findOrFail($this->atendimentoId);
            Gate::authorize('update', $atendimentoExistente);
        } else {
            Gate::authorize('create', Atendimento::class);
        }
        
        $this->verificarConflitos();

        if (!empty($this->erros)) {
            return;
        }

        $this->validate([
            'pacienteId' => 'required|exists:pacientes,id',
            'userId' => 'required|exists:users,id',
            'salaId' => 'nullable|exists:salas,id',
            'dataHoraInicio' => 'required|date',
            'dataHoraFim' => 'required|date|after:dataHoraInicio',
            'status' => 'required|in:Agendado,Confirmado,Check-in,Concluído,Cancelado',
        ]);

        $scope = $this->resolveUnitScope();
        if (is_array($scope)) {
            if (empty($scope)) {
                session()->flash('error', 'Nenhuma unidade disponível para agendar atendimentos.');
                return;
            }

            $paciente = Paciente::findOrFail($this->pacienteId);
            if (!$paciente->unidade_padrao_id || !in_array((int) $paciente->unidade_padrao_id, $scope, true)) {
                session()->flash('error', 'O paciente selecionado pertence a uma unidade não autorizada.');
                return;
            }

            $profissional = User::with('unidades', 'roles')->findOrFail($this->userId);
            if (!$profissional->hasAnyRole(['Profissional', 'Coordenador'])) {
                session()->flash('error', 'Selecione um profissional válido.');
                return;
            }

            $profissionalUnits = $profissional->unidades
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (empty(array_intersect($profissionalUnits, $scope))) {
                session()->flash('error', 'O profissional selecionado não pertence às suas unidades.');
                return;
            }

            if ($this->salaId) {
                $sala = Sala::findOrFail($this->salaId);
                if (!in_array((int) $sala->unidade_id, $scope, true)) {
                    session()->flash('error', 'Selecione uma sala da sua unidade.');
                    return;
                }
            }
        }

        // Fluxo 1.4: Verificação de conflitos para recorrência
        $recorrenciaId = $this->repetir ? \Illuminate\Support\Str::uuid()->toString() : null;
        $atendimentosCriados = [];

        $vezes = $this->repetir ? $this->vezesRepetir : 1;
        $inicio = Carbon::parse($this->dataHoraInicio);
        $fim = Carbon::parse($this->dataHoraFim);
        $duracao = $inicio->diffInMinutes($fim);

        // Verifica conflitos para cada ocorrência (Fluxo 1.4)
        for ($i = 0; $i < $vezes; $i++) {
            $semanasAdicionar = $i * ($this->tipoRecorrencia === 'semanal' ? 1 : ($this->tipoRecorrencia === 'quinzenal' ? 2 : 4));
            $dataInicio = $inicio->copy()->addWeeks($semanasAdicionar);
            $dataFim = $dataInicio->copy()->addMinutes($duracao);

            // Verifica conflitos para esta ocorrência específica
            $conflitosOcorrencia = $this->verificarConflitosParaData($dataInicio, $dataFim);
            if (!empty($conflitosOcorrencia)) {
                $this->erros[] = "Conflito detectado na ocorrência " . ($i + 1) . " (" . $dataInicio->format('d/m/Y H:i') . "): " . implode(', ', $conflitosOcorrencia);
                return; // Para e não cria nenhum atendimento
            }
        }

        // Se todas as verificações passaram, cria os atendimentos
        for ($i = 0; $i < $vezes; $i++) {
            $semanasAdicionar = $i * ($this->tipoRecorrencia === 'semanal' ? 1 : ($this->tipoRecorrencia === 'quinzenal' ? 2 : 4));
            $dataInicio = $inicio->copy()->addWeeks($semanasAdicionar);
            $dataFim = $dataInicio->copy()->addMinutes($duracao);

            if ($atendimentoExistente && $i === 0) {
                $atendimentoExistente->update([
                    'paciente_id' => $this->pacienteId,
                    'user_id' => $this->userId,
                    'sala_id' => $this->salaId,
                    'status' => $this->status,
                    'data_hora_inicio' => $dataInicio,
                    'data_hora_fim' => $dataFim,
                    'recorrencia_id' => $recorrenciaId,
                ]);
                $atendimentosCriados[] = $atendimentoExistente->fresh();
            } else {
                // Cria novo atendimento
                $atendimento = Atendimento::create([
                    'paciente_id' => $this->pacienteId,
                    'user_id' => $this->userId,
                    'sala_id' => $this->salaId,
                    'status' => $this->status,
                    'data_hora_inicio' => $dataInicio,
                    'data_hora_fim' => $dataFim,
                    'recorrencia_id' => $recorrenciaId,
                ]);
                $atendimentosCriados[] = $atendimento;
            }
        }

        // Dispara eventos para broadcast
        foreach ($atendimentosCriados as $atendimento) {
            event(new \App\Events\AtendimentoAtualizado($atendimento));
            
            if ($atendimento->status === 'Concluído') {
                event(new \App\Events\AtendimentoConcluido($atendimento));
            }
        }

        session()->flash('message', $this->atendimentoId ? 'Atendimento atualizado!' : 'Atendimento(s) criado(s) com sucesso!');
        $this->dispatch('atendimento-salvo');
        $this->dispatch('fechar-modal-atendimento');
    }

    public function render()
    {
        $scope = $this->resolveUnitScope();

        if (is_array($scope)) {
            if (empty($scope)) {
                $pacientes = collect();
            } else {
                $pacientes = Paciente::where('status', 'Ativo')
                    ->whereIn('unidade_padrao_id', $scope)
                    ->orderBy('nome_completo')
                    ->get();
            }
        } else {
            $pacientes = Paciente::where('status', 'Ativo')
                ->orderBy('nome_completo')
                ->get();
        }

        $profissionaisQuery = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Profissional', 'Coordenador']);
        });

        if (is_array($scope)) {
            if (empty($scope)) {
                $profissionais = collect();
            } else {
                $profissionais = $profissionaisQuery
                    ->whereHas('unidades', fn ($q) => $q->whereIn('unidades.id', $scope))
                    ->orderBy('name')
                    ->get();
            }
        } else {
            $profissionais = $profissionaisQuery->orderBy('name')->get();
        }

        $salasQuery = Sala::query();
        if (is_array($scope)) {
            if (empty($scope)) {
                $salas = collect();
            } else {
                $salas = $salasQuery
                    ->whereIn('unidade_id', $scope)
                    ->orderBy('nome')
                    ->get();
            }
        } else {
            $salas = $salasQuery->orderBy('nome')->get();
        }

        return view('livewire.form-atendimento', [
            'pacientes' => $pacientes,
            'profissionais' => $profissionais,
            'salas' => $salas,
        ]);
    }

    /**
     * Resolve a lista de unidades às quais o usuário atual tem acesso.
     *
     * @return array<int>|null Retorna null para acesso ilimitado (Admin sem unidade selecionada),
     *                         array vazia quando não há unidades associadas, ou lista de IDs permitidos.
     */
    protected function resolveUnitScope(): ?array
    {
        $user = Auth::user()->loadMissing('unidades');

        if ($user->hasRole('Admin')) {
            $selecionada = session('unidade_selecionada');
            return $selecionada ? [(int) $selecionada] : null;
        }

        $unitIds = $user->unidades
            ->pluck('id')
            ->filter(fn ($id) => !is_null($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (empty($unitIds)) {
            return [];
        }

        $selecionada = session('unidade_selecionada');
        if ($selecionada && in_array((int) $selecionada, $unitIds, true)) {
            return [(int) $selecionada];
        }

        return $unitIds;
    }
}
