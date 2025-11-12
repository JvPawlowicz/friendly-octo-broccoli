<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Coordenador</h1>
                <p class="mt-2 text-sm text-gray-600">Visão gerencial da unidade</p>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                            <p class="text-purple-100 text-sm font-medium">Avaliações em Rascunho</p>
                            <p class="text-3xl font-bold mt-2">{{ $avaliacoesRascunho->count() }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total de Pacientes</p>
                            <p class="text-3xl font-bold mt-2">{{ $totalPacientes }}</p>
                            <p class="text-green-100 text-xs mt-1">{{ $totalProfissionais }} profissionais</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                            <a href="{{ route('app.evolucoes') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                Ver todas →
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-gray-100">
                            @forelse($evolucoesPendentes->take(5) as $ev)
                                <li class="py-3 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $ev->paciente->nome_completo }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Profissional: {{ $ev->profissional->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <a href="{{ route('app.evolucoes.edit', $ev->id) }}" 
                                           class="ml-4 text-xs text-indigo-600 hover:text-indigo-800">
                                            Editar →
                                        </a>
                                    </div>
                                </li>
                            @empty
                                <li class="py-8 text-center">
                                    <p class="text-sm text-gray-500">Nenhuma evolução pendente</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Avaliações em Rascunho -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-900">Avaliações em Rascunho</h2>
                            </div>
                            <a href="{{ route('app.avaliacoes-unidade') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                Ver todas →
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-gray-100">
                            @forelse($avaliacoesRascunho->take(5) as $av)
                                <li class="py-3 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $av->template->nome_template }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $av->paciente->nome_completo }} • {{ $av->profissional->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <a href="{{ route('app.avaliacoes.edit', $av->id) }}" 
                                           class="ml-4 text-xs text-indigo-600 hover:text-indigo-800">
                                            Editar →
                                        </a>
                                    </div>
                                </li>
                            @empty
                                <li class="py-8 text-center">
                                    <p class="text-sm text-gray-500">Nenhuma avaliação em rascunho</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Produtividade por Profissional -->
            @if($produtividadeProfissionais->isNotEmpty())
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mt-6">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Produtividade (Últimos 30 dias)</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($produtividadeProfissionais as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-semibold">
                                            {{ strtoupper(substr($item->profissional->name ?? 'N/A', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $item->profissional->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">{{ $item->total }}</p>
                                    <p class="text-xs text-gray-500">atendimentos</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Ações Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <a href="{{ route('app.templates-avaliacao') }}" 
                   class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Templates de Avaliação</h3>
                            <p class="text-xs text-gray-500 mt-1">Gerenciar templates</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('app.unidades-salas') }}" 
                   class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Unidades e Salas</h3>
                            <p class="text-xs text-gray-500 mt-1">Gerenciar unidades</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('app.avaliacoes-unidade') }}" 
                   class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Avaliações da Unidade</h3>
                            <p class="text-xs text-gray-500 mt-1">Ver todas as avaliações</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

