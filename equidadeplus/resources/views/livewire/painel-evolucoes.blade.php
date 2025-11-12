<div>
    <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-2 text-sm text-gray-600">Visão geral dos seus atendimentos e evoluções pendentes</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase font-semibold text-indigo-500">Notas rápidas</p>
                            <h2 class="text-lg font-semibold text-gray-900">Planeje o seu dia</h2>
                            @if($notaAtualizadaEm)
                                <p class="text-xs text-gray-400">Atualizada em {{ $notaAtualizadaEm }}</p>
                            @endif
                        </div>
                        <button wire:click="salvarNotaRapida" type="button"
                                class="inline-flex items-center px-3 py-2 border border-indigo-200 text-sm font-medium text-indigo-600 rounded-lg hover:bg-indigo-50 transition">
                            Salvar nota
                        </button>
                    </div>
                    <textarea wire:model.defer="notaRapida" rows="5"
                              class="w-full rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-700 p-4"
                              placeholder="Liste prioridades, pacientes críticos ou lembretes para o plantão."></textarea>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                    <p class="text-xs uppercase font-semibold text-amber-500">Pendências</p>
                    <h2 class="text-lg font-semibold text-gray-900 mt-1">Checklist do dia</h2>
                    <ul class="mt-4 space-y-3 text-sm text-gray-600">
                        <li class="flex items-center justify-between">
                            <span>Evoluções em rascunho</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-600">
                                {{ $pendenciasDashboard['evolucoes'] ?? 0 }}
                            </span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Avaliações pendentes</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600">
                                {{ $pendenciasDashboard['avaliacoes'] ?? 0 }}
                            </span>
                        </li>
                    </ul>

                    <div class="mt-5">
                        <p class="text-xs uppercase font-semibold text-slate-400">Avaliações mais urgentes</p>
                        <ul class="mt-3 space-y-2">
                            @forelse($avaliacoesPendentes->take(3) as $avaliacao)
                                <li class="rounded-lg border border-slate-100 px-3 py-2">
                                    <p class="text-sm font-medium text-slate-800">{{ $avaliacao->paciente->nome_completo }}</p>
                                    <p class="text-xs text-slate-400">{{ $avaliacao->template->nome_template ?? 'Avaliação' }} • {{ $avaliacao->created_at->format('d/m/Y') }}</p>
                                </li>
                            @empty
                                <li class="text-xs text-slate-400">Nenhuma avaliação pendente.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Atendimentos Hoje</p>
                            <p class="text-3xl font-bold mt-2">{{ $atendimentosHoje->count() }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">Evoluções Pendentes</p>
                            <p class="text-3xl font-bold mt-2">{{ $evolucoesPendentes->count() }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Total Evoluções</p>
                            <p class="text-3xl font-bold mt-2">{{ $totalEvolucoes }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Finalizadas</p>
                            <p class="text-3xl font-bold mt-2">{{ $finalizadas }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Abas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button wire:click="trocarAba('pendentes')" 
                                class="px-6 py-4 text-sm font-medium border-b-2 transition-colors {{ $abaAtiva === 'pendentes' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Evoluções Pendentes
                        </button>
                        <button wire:click="trocarAba('todas')" 
                                class="px-6 py-4 text-sm font-medium border-b-2 transition-colors {{ $abaAtiva === 'todas' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Todas as Evoluções
                        </button>
                    </nav>
                </div>
            </div>

            @if($abaAtiva === 'pendentes')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Atendimentos de Hoje -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Atendimentos de Hoje</h2>
                        </div>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-gray-100">
                            @forelse($atendimentosHoje as $at)
                                <li class="py-4 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                                        {{ $at->data_hora_inicio->format('H:i') }}
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $at->paciente->nome_completo }}</p>
                                                    @if($at->sala)
                                                        <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                            </svg>
                                                            {{ $at->sala->nome }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <span class="ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($at->status)
                                                @case('Agendado') bg-gray-100 text-gray-800 @break
                                                @case('Confirmado') bg-blue-100 text-blue-800 @break
                                                @case('Check-in') bg-green-100 text-green-800 @break
                                            @endswitch
                                        ">
                                            {{ $at->status }}
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="py-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Nenhum atendimento agendado para hoje</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Evoluções Pendentes -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-900">Evoluções Pendentes</h2>
                            </div>
                            @if($evolucoesPendentes->count() > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $evolucoesPendentes->count() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-gray-100">
                            @forelse($evolucoesPendentes as $ev)
                                <li class="py-4 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $ev->paciente->nome_completo }}</p>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Atendimento: {{ $ev->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <button wire:click="editarEvolucao({{ $ev->id }})" 
                                                class="ml-4 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Preencher
                                        </button>
                                    </div>
                                </li>
                            @empty
                                <li class="py-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Nenhuma evolução pendente</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                            </div>
                        @else
            <!-- Aba: Todas as Evoluções -->
            <!-- Filtros -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   placeholder="Paciente ou relato..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model.live="statusFilter" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="Rascunho">Rascunho</option>
                                <option value="Finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                            <select wire:model.live="pacienteFilter" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id }}">{{ $paciente->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                            <input type="date" wire:model.live="dataInicio" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                            <input type="date" wire:model.live="dataFim" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button wire:click="limparFiltros" 
                                class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Limpar filtros
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Todas as Evoluções -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    @if($todasEvolucoes && $todasEvolucoes->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma evolução encontrada</h3>
                            <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros ou criar uma nova evolução.</p>
                        </div>
                    @elseif($todasEvolucoes)
                            <div class="space-y-4">
                            @foreach($todasEvolucoes as $evolucao)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $evolucao->paciente->nome_completo }}
                                                </h3>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    {{ $evolucao->status === 'Finalizado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $evolucao->status }}
                                                </span>
                                            </div>
                                            @if($evolucao->relato_clinico)
                                                <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                                    {{ \Illuminate\Support\Str::limit($evolucao->relato_clinico, 150) }}
                                                </p>
                                            @endif
                                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                <span>
                                                    <strong>Data:</strong> {{ $evolucao->created_at->format('d/m/Y H:i') }}
                                                </span>
                                                @if($evolucao->profissional)
                                                    <span>
                                                        <strong>Profissional:</strong> {{ $evolucao->profissional->name }}
                                                    </span>
                                                @endif
                                                @if($evolucao->finalizado_em)
                                                    <span>
                                                        <strong>Finalizada em:</strong> {{ $evolucao->finalizado_em->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            @if($evolucao->status === 'Rascunho')
                                                <a href="{{ route('app.evolucoes.edit', $evolucao->id) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded hover:bg-indigo-200 transition">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Editar
                                                </a>
                                            @endif
                                            <a href="{{ route('app.pacientes.prontuario', $evolucao->paciente_id) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Ver Prontuário
                                            </a>
                                        </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        <div class="mt-6">
                            {{ $todasEvolucoes->links() }}
                        </div>
                        @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Escutar eventos do Echo (tempo real - Fluxo 2.3)
        @if(config('broadcasting.default') === 'reverb')
        if (typeof Echo !== 'undefined') {
            Echo.private('user.{{ Auth::id() }}')
                .listen('EvolucaoPendenteCriada', (e) => {
                    @this.call('atualizarLista');
                });
        }
        @endif
    </script>
    @endpush
</div>
