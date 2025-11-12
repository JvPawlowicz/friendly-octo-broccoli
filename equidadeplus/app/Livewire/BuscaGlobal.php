<?php

namespace App\Livewire;

use App\Models\Paciente;
use App\Models\Atendimento;
use App\Models\Evolucao;
use App\Models\Avaliacao;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BuscaGlobal extends Component
{
    public $query = '';
    public $resultados = [];
    public $mostrarResultados = false;

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->resultados = [];
            $this->mostrarResultados = false;
            return;
        }

        $this->buscar();
    }

    public function buscar()
    {
        $termo = $this->query;
        $user = Auth::user();
        $unidadeSelecionada = session('unidade_selecionada');

        $this->resultados = [
            'pacientes' => [],
            'atendimentos' => [],
            'evolucoes' => [],
            'avaliacoes' => [],
        ];

        // Busca pacientes
        $queryPacientes = Paciente::where('nome_completo', 'like', '%' . $termo . '%')
            ->orWhere('cpf', 'like', '%' . $termo . '%')
            ->orWhere('email_principal', 'like', '%' . $termo . '%');

        if ($unidadeSelecionada) {
            $queryPacientes->where('unidade_padrao_id', $unidadeSelecionada);
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryPacientes->whereIn('unidade_padrao_id', $unidadeIds);
        }

        $this->resultados['pacientes'] = $queryPacientes->limit(5)->get();

        // Busca atendimentos
        $queryAtendimentos = Atendimento::whereHas('paciente', function ($q) use ($termo) {
            $q->where('nome_completo', 'like', '%' . $termo . '%');
        })->with(['paciente', 'profissional', 'sala']);

        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $queryAtendimentos->where('user_id', $user->id);
        } elseif ($unidadeSelecionada) {
            $queryAtendimentos->whereHas('sala', function ($q) use ($unidadeSelecionada) {
                $q->where('unidade_id', $unidadeSelecionada);
            });
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryAtendimentos->whereHas('sala', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_id', $unidadeIds);
            });
        }

        $this->resultados['atendimentos'] = $queryAtendimentos->limit(5)->get();

        // Busca evoluções
        $queryEvolucoes = Evolucao::whereNull('evolucao_pai_id')
            ->where(function ($q) use ($termo) {
                $q->where('relato_clinico', 'like', '%' . $termo . '%')
                  ->orWhereHas('paciente', function ($q2) use ($termo) {
                      $q2->where('nome_completo', 'like', '%' . $termo . '%');
                  });
            })
            ->with(['paciente', 'profissional']);

        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $queryEvolucoes->where('user_id', $user->id);
        } elseif ($unidadeSelecionada) {
            $queryEvolucoes->whereHas('paciente', function ($q) use ($unidadeSelecionada) {
                $q->where('unidade_padrao_id', $unidadeSelecionada);
            });
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryEvolucoes->whereHas('paciente', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_padrao_id', $unidadeIds);
            });
        }

        $this->resultados['evolucoes'] = $queryEvolucoes->limit(5)->get();

        // Busca avaliações
        $queryAvaliacoes = Avaliacao::whereHas('paciente', function ($q) use ($termo) {
            $q->where('nome_completo', 'like', '%' . $termo . '%');
        })->orWhereHas('template', function ($q) use ($termo) {
            $q->where('nome_template', 'like', '%' . $termo . '%');
        })->with(['paciente', 'template', 'profissional']);

        if ($user->hasRole('Profissional') && !$user->hasAnyRole(['Admin', 'Coordenador'])) {
            $queryAvaliacoes->where('user_id', $user->id);
        } elseif ($unidadeSelecionada) {
            $queryAvaliacoes->whereHas('paciente', function ($q) use ($unidadeSelecionada) {
                $q->where('unidade_padrao_id', $unidadeSelecionada);
            });
        } elseif (!$user->hasRole('Admin')) {
            $unidadeIds = $user->unidades->pluck('id')->toArray();
            $queryAvaliacoes->whereHas('paciente', function ($q) use ($unidadeIds) {
                $q->whereIn('unidade_padrao_id', $unidadeIds);
            });
        }

        $this->resultados['avaliacoes'] = $queryAvaliacoes->limit(5)->get();

        $this->mostrarResultados = true;
    }

    public function fecharResultados()
    {
        $this->mostrarResultados = false;
        $this->query = '';
        $this->resultados = [];
    }

    public function render()
    {
        return view('livewire.busca-global');
    }
}

