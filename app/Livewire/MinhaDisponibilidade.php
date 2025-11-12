<?php

namespace App\Livewire;

use App\Models\DisponibilidadeUsuario;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class MinhaDisponibilidade extends Component
{
    public $disponibilidades = [];
    public $mostrarModal = false;
    public $disponibilidadeId = null;
    public $dia_da_semana = '';
    public $hora_inicio = '';
    public $hora_fim = '';

    public function mount()
    {
        // Apenas profissionais podem acessar
        if (!Auth::user()->hasRole('Profissional') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Apenas profissionais podem gerenciar disponibilidade.');
        }

        $this->carregarDisponibilidades();
    }

    public function carregarDisponibilidades()
    {
        $this->disponibilidades = DisponibilidadeUsuario::where('user_id', Auth::id())
            ->orderBy('dia_da_semana')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('dia_da_semana');
    }

    public function abrirModal($disponibilidadeId = null)
    {
        $this->disponibilidadeId = $disponibilidadeId;
        $this->mostrarModal = true;

        if ($disponibilidadeId) {
            $disponibilidade = DisponibilidadeUsuario::where('id', $disponibilidadeId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $this->dia_da_semana = $disponibilidade->dia_da_semana;
            $this->hora_inicio = $disponibilidade->hora_inicio;
            $this->hora_fim = $disponibilidade->hora_fim;
        } else {
            $this->reset(['dia_da_semana', 'hora_inicio', 'hora_fim']);
        }
    }

    public function fecharModal()
    {
        $this->mostrarModal = false;
        $this->reset(['disponibilidadeId', 'dia_da_semana', 'hora_inicio', 'hora_fim']);
    }

    public function salvar()
    {
        $this->validate([
            'dia_da_semana' => 'required|integer|between:0,6',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
        ], [
            'dia_da_semana.required' => 'Selecione um dia da semana.',
            'hora_inicio.required' => 'Informe o horário de início.',
            'hora_fim.required' => 'Informe o horário de término.',
            'hora_fim.after' => 'O horário de término deve ser após o horário de início.',
        ]);

        // Verifica se já existe disponibilidade no mesmo horário
        $existe = DisponibilidadeUsuario::where('user_id', Auth::id())
            ->where('dia_da_semana', $this->dia_da_semana)
            ->where(function ($q) {
                $q->whereBetween('hora_inicio', [$this->hora_inicio, $this->hora_fim])
                  ->orWhereBetween('hora_fim', [$this->hora_inicio, $this->hora_fim])
                  ->orWhere(function ($q2) {
                      $q2->where('hora_inicio', '<=', $this->hora_inicio)
                         ->where('hora_fim', '>=', $this->hora_fim);
                  });
            });

        if ($this->disponibilidadeId) {
            $existe->where('id', '!=', $this->disponibilidadeId);
        }

        if ($existe->exists()) {
            session()->flash('error', 'Já existe uma disponibilidade cadastrada neste horário.');
            return;
        }

        if ($this->disponibilidadeId) {
            $disponibilidade = DisponibilidadeUsuario::where('id', $this->disponibilidadeId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $disponibilidade->update([
                'dia_da_semana' => $this->dia_da_semana,
                'hora_inicio' => $this->hora_inicio,
                'hora_fim' => $this->hora_fim,
            ]);

            session()->flash('message', 'Disponibilidade atualizada com sucesso!');
        } else {
            DisponibilidadeUsuario::create([
                'user_id' => Auth::id(),
                'dia_da_semana' => $this->dia_da_semana,
                'hora_inicio' => $this->hora_inicio,
                'hora_fim' => $this->hora_fim,
            ]);

            session()->flash('message', 'Disponibilidade cadastrada com sucesso!');
        }

        $this->carregarDisponibilidades();
        $this->fecharModal();
    }

    public function deletar($disponibilidadeId)
    {
        $disponibilidade = DisponibilidadeUsuario::where('id', $disponibilidadeId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $disponibilidade->delete();

        session()->flash('message', 'Disponibilidade removida com sucesso!');
        $this->carregarDisponibilidades();
    }

    public function getDiaSemanaNome($dia)
    {
        $dias = [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
        ];

        return $dias[$dia] ?? 'Desconhecido';
    }

    public function render()
    {
        return view('livewire.minha-disponibilidade');
    }
}

