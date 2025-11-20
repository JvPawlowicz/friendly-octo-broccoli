<?php

namespace App\Livewire;

use App\Models\DisponibilidadeUsuario;
use App\Models\User;
use App\Models\Unidade;
use App\Models\Atendimento;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class MinhaDisponibilidade extends Component
{
    public $mostrarModal = false;
    public $disponibilidadeId = null;
    public $dia_da_semana = '';
    public $hora_inicio = '';
    public $hora_fim = '';
    
    // Filtros para Admin
    public $profissionalFiltro = null;
    public $unidadeFiltro = null;

    public function mount()
    {
        // Apenas profissionais, coordenadores e admin podem acessar
        if (!Auth::user()->hasRole('Profissional') && !Auth::user()->hasAnyRole(['Admin', 'Coordenador'])) {
            abort(403, 'Apenas profissionais podem gerenciar disponibilidade.');
        }
    }

    public function getDisponibilidadesProperty()
    {
        return DisponibilidadeUsuario::where('user_id', Auth::id())
            ->orderBy('dia_da_semana')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('dia_da_semana');
    }
    
    // Para Admin: disponibilidades de todos os profissionais
    public function getDisponibilidadesEquipeProperty()
    {
        $query = DisponibilidadeUsuario::with('user.unidades')
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', function ($roleQuery) {
                    $roleQuery->whereIn('name', ['Profissional', 'Admin']);
                });
            });
        
        if ($this->profissionalFiltro) {
            $query->where('user_id', $this->profissionalFiltro);
        }
        
        if ($this->unidadeFiltro) {
            $query->whereHas('user.unidades', function ($q) {
                $q->where('unidades.id', $this->unidadeFiltro);
            });
        }
        
        return $query->orderBy('dia_da_semana')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy(['user_id', 'dia_da_semana']);
    }
    
    public function getProfissionaisProperty()
    {
        return User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Profissional', 'Admin']);
        })
        ->orderBy('name')
        ->get();
    }
    
    public function getUnidadesProperty()
    {
        return Unidade::orderBy('nome')->get();
    }
    
    // Calcula cobertura por dia/horário
    public function getCoberturaProperty()
    {
        $disponibilidades = $this->disponibilidadesEquipe;
        $cobertura = [];
        
        // Para cada dia da semana
        for ($dia = 0; $dia <= 6; $dia++) {
            $cobertura[$dia] = [];
            
            // Para cada profissional
            foreach ($disponibilidades as $userId => $dias) {
                if (isset($dias[$dia])) {
                    foreach ($dias[$dia] as $disp) {
                        $inicio = strtotime($disp->hora_inicio);
                        $fim = strtotime($disp->hora_fim);
                        
                        // Cria slots de 30 em 30 minutos
                        for ($hora = $inicio; $hora < $fim; $hora += 1800) {
                            $slot = date('H:i', $hora);
                            if (!isset($cobertura[$dia][$slot])) {
                                $cobertura[$dia][$slot] = [];
                            }
                            $cobertura[$dia][$slot][] = $userId;
                        }
                    }
                }
            }
        }
        
        return $cobertura;
    }
    
    // Calcula horários livres (disponibilidade - atendimentos agendados)
    public function getHorariosLivresProperty()
    {
        if (!Auth::user()->hasRole('Admin')) {
            return [];
        }
        
        $horariosLivres = [];
        $disponibilidades = $this->disponibilidadesEquipe;
        
        // Para cada profissional
        foreach ($disponibilidades as $userId => $dias) {
            $horariosLivres[$userId] = [];
            
            // Para cada dia da semana
            for ($dia = 0; $dia <= 6; $dia++) {
                if (!isset($dias[$dia])) {
                    continue;
                }
                
                $horariosLivres[$userId][$dia] = [];
                
                // Para cada disponibilidade do dia
                foreach ($dias[$dia] as $disp) {
                    $inicio = Carbon::parse($disp->hora_inicio);
                    $fim = Carbon::parse($disp->hora_fim);
                    
                    // Busca atendimentos agendados para este profissional neste dia da semana
                    // Para calcular horários livres, verificamos a próxima ocorrência deste dia
                    $hoje = Carbon::now();
                    $diasParaProximoDia = ($dia - $hoje->dayOfWeek + 7) % 7;
                    if ($diasParaProximoDia == 0 && $hoje->format('H:i') >= $fim->format('H:i')) {
                        $diasParaProximoDia = 7; // Se já passou o horário hoje, pega a próxima semana
                    }
                    $proximoDia = $hoje->copy()->addDays($diasParaProximoDia)->startOfDay();
                    
                    // Busca atendimentos para os próximos 30 dias neste dia da semana
                    // SQLite usa strftime('%w', ...) onde 0=Domingo, 6=Sábado
                    $atendimentos = Atendimento::where('user_id', $userId)
                        ->whereBetween('data_hora_inicio', [
                            $proximoDia->copy()->startOfDay(),
                            $proximoDia->copy()->addDays(30)->endOfDay()
                        ])
                        ->whereRaw("CAST(strftime('%w', data_hora_inicio) AS INTEGER) = ?", [$dia])
                        ->whereIn('status', ['Agendado', 'Confirmado', 'Check-in'])
                        ->get();
                    
                    // Cria slots de 30 em 30 minutos
                    $slotAtual = $inicio->copy();
                    while ($slotAtual->lt($fim)) {
                        $slotFim = $slotAtual->copy()->addMinutes(30);
                        
                        // Verifica se há atendimento neste slot (qualquer dia da semana)
                        $temAtendimento = $atendimentos->contains(function ($atendimento) use ($slotAtual, $slotFim) {
                            $atendInicio = Carbon::parse($atendimento->data_hora_inicio);
                            $atendFim = Carbon::parse($atendimento->data_hora_fim);
                            
                            $atendHoraInicio = $atendInicio->format('H:i');
                            $atendHoraFim = $atendFim->format('H:i');
                            $slotHoraInicio = $slotAtual->format('H:i');
                            $slotHoraFim = $slotFim->format('H:i');
                            
                            // Verifica sobreposição de horários
                            return ($slotHoraInicio < $atendHoraFim && $slotHoraFim > $atendHoraInicio);
                        });
                        
                        if (!$temAtendimento) {
                            $horariosLivres[$userId][$dia][] = [
                                'inicio' => $slotAtual->format('H:i'),
                                'fim' => $slotFim->format('H:i'),
                            ];
                        }
                        
                        $slotAtual->addMinutes(30);
                    }
                }
            }
        }
        
        return $horariosLivres;
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

            $this->dispatch('app:toast', message: 'Disponibilidade atualizada com sucesso!', type: 'success');
        } else {
            DisponibilidadeUsuario::create([
                'user_id' => Auth::id(),
                'dia_da_semana' => $this->dia_da_semana,
                'hora_inicio' => $this->hora_inicio,
                'hora_fim' => $this->hora_fim,
            ]);

            $this->dispatch('app:toast', message: 'Disponibilidade cadastrada com sucesso!', type: 'success');
        }

        $this->fecharModal();
    }

    public function deletar($disponibilidadeId)
    {
        $disponibilidade = DisponibilidadeUsuario::where('id', $disponibilidadeId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $disponibilidade->delete();

        $this->dispatch('app:toast', message: 'Disponibilidade removida com sucesso!', type: 'success');
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
        $user = Auth::user();
        
        // Admin vê a visão da equipe + pode gerenciar própria disponibilidade
        if ($user->hasRole('Admin')) {
            return view('livewire.disponibilidade-equipe', [
                'profissionais' => $this->profissionais,
                'unidades' => $this->unidades,
                'disponibilidadesEquipe' => $this->disponibilidadesEquipe,
                'cobertura' => $this->cobertura,
                'horariosLivres' => $this->horariosLivres,
                'minhasDisponibilidades' => $this->disponibilidades,
            ]);
        }
        
        // Profissionais e Coordenadores veem apenas sua disponibilidade
        return view('livewire.minha-disponibilidade');
    }
}

