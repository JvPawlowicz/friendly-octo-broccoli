<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui.breadcrumbs :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Avaliações']
            ]" />
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                @if($scope === 'minhas')
                                    Minhas Avaliações
                                @else
                                    Avaliações da Unidade
                                @endif
                            </h1>
                            <p class="text-gray-600">
                                @if($scope === 'minhas')
                                    Visualize e gerencie todas as suas avaliações aplicadas
                                @else
                                    Visualize e gerencie todas as avaliações da unidade
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('app.avaliacoes.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Nova Avaliação
                        </a>
                    </div>

                    <!-- Estatísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $estatisticas['total'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-4 rounded-lg border border-yellow-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Em Rascunho</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $estatisticas['rascunho'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg border border-green-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Finalizadas</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $estatisticas['finalizadas'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-{{ $scope === 'unidade' ? '6' : '5' }} gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   placeholder="Paciente ou template..."
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
                        @if($scope === 'unidade')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Profissional</label>
                                <select wire:model.live="profissionalFilter" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos</option>
                                    @foreach($profissionais as $profissional)
                                        <option value="{{ $profissional->id }}">{{ $profissional->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
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
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Avaliações -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($avaliacoes as $avaliacao)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $avaliacao->template->nome_template ?? 'Template não encontrado' }}
                                            </h3>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $avaliacao->status === 'Finalizado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $avaliacao->status }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 space-y-1">
                                            <p><span class="font-medium">Paciente:</span> {{ $avaliacao->paciente->nome_completo }}</p>
                                            @if($scope === 'unidade')
                                                <p><span class="font-medium">Profissional:</span> {{ $avaliacao->profissional->name ?? 'N/A' }}</p>
                                            @endif
                                            <p><span class="font-medium">Data:</span> {{ $avaliacao->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('app.avaliacoes.edit', $avaliacao->id) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-indigo-300 rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Ver/Editar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma avaliação encontrada</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @if($scope === 'minhas')
                                        Você ainda não aplicou nenhuma avaliação.
                                    @else
                                        Não há avaliações na unidade com os filtros selecionados.
                                    @endif
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $avaliacoes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

