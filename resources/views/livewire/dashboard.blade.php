<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    @if($role === 'admin')
                        Dashboard Administrativo
                    @elseif($role === 'coordenador')
                        Dashboard Coordenador
                    @elseif($role === 'secretaria')
                        Dashboard Secretaria
                    @else
                        Painel Clínico
                    @endif
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    @if($role === 'admin')
                        Visão geral de todas as unidades e atividades do sistema
                    @elseif($role === 'coordenador')
                        Visão gerencial da unidade
                    @elseif($role === 'secretaria')
                        Visão geral das atividades administrativas
                    @else
                        Suas pendências e atividades do dia
                    @endif
                </p>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @if($role === 'admin')
                    <!-- Admin Cards -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total de Unidades</p>
                                <p class="text-3xl font-bold mt-2">{{ $totalUnidades ?? 0 }}</p>
                            </div>
                            <div class="bg-white/20 rounded-full p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Total de Pacientes</p>
                                <p class="text-3xl font-bold mt-2">{{ $totalPacientes ?? 0 }}</p>
                            </div>
                            <div class="bg-white/20 rounded-full p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Profissionais</p>
                                <p class="text-3xl font-bold mt-2">{{ $totalProfissionais ?? 0 }}</p>
                            </div>
                            <div class="bg-white/20 rounded-full p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Atendimentos (Mês)</p>
                                <p class="text-3xl font-bold mt-2">{{ $totalAtendimentosMes ?? 0 }}</p>
                            </div>
                            <div class="bg-white/20 rounded-full p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @elseif($role === 'coordenador')
                    <!-- Coordenador Cards -->
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
                                <p class="text-3xl font-bold mt-2">{{ $totalPacientes ?? 0 }}</p>
                                <p class="text-green-100 text-xs mt-1">{{ $totalProfissionais ?? 0 }} profissionais</p>
                            </div>
                            <div class="bg-white/20 rounded-full p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @elseif($role === 'secretaria')
                    <!-- Secretaria Cards -->
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
                                <p class="text-yellow-100 text-sm font-medium">Pacientes Aguardando</p>
                                <p class="text-3xl font-bold mt-2">{{ $pacientesAguardando ?? 0 }}</p>
                            </div>
                            <div class="bg-white/20 rounded-full p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Documentos Pendentes</p>
                                <p class="text-3xl font-bold mt-2">{{ $documentosPendentes ?? 0 }}</p>
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
                                <p class="text-green-100 text-sm font-medium">Total de Pacientes</p>
                                <p class="text-3xl font-bold mt-2">{{ $totalPacientes ?? 0 }}</p>
                                <p class="text-green-100 text-xs mt-1">+{{ $pacientesNovosMes ?? 0 }} este mês</p>
                            </div>
                            <div class="bg-white/20 rounded-full p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Profissional Cards -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Meus Atendimentos Hoje</p>
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
                                <p class="text-green-100 text-sm font-medium">Meus Pacientes</p>
                                <p class="text-3xl font-bold mt-2">{{ $totalPacientes ?? 0 }}</p>
                            </div>
                            <div class="bg-white/20 rounded-full p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Listas e Tabelas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @if($role !== 'secretaria' && isset($evolucoesPendentes))
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
                                @forelse($evolucoesPendentes as $ev)
                                    <li class="py-3 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $ev->paciente->nome_completo ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $ev->profissional->name ?? 'N/A' }} • {{ $ev->paciente->unidadePadrao->nome ?? 'N/A' }}
                                                </p>
                                            </div>
                                            <a href="{{ route('app.evolucoes.edit', $ev->id) }}" 
                                               class="ml-4 text-xs text-indigo-600 hover:text-indigo-800">
                                                Ver →
                                            </a>
                                        </div>
                                    </li>
                                @empty
                                    <li class="py-8 text-center text-gray-500">
                                        <p>Nenhuma evolução pendente</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @endif

                @if($role !== 'secretaria' && isset($avaliacoesRascunho))
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
                                <a href="{{ route('app.avaliacoes.list') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    Ver todas →
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <ul class="divide-y divide-gray-100">
                                @forelse($avaliacoesRascunho as $av)
                                    <li class="py-3 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $av->paciente->nome_completo ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $av->template->nome ?? 'N/A' }} • {{ $av->profissional->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                            <a href="{{ route('app.avaliacoes.edit', $av->id) }}" 
                                               class="ml-4 text-xs text-indigo-600 hover:text-indigo-800">
                                                Ver →
                                            </a>
                                        </div>
                                    </li>
                                @empty
                                    <li class="py-8 text-center text-gray-500">
                                        <p>Nenhuma avaliação em rascunho</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Atendimentos de Hoje -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 {{ ($role === 'secretaria' || ($role !== 'secretaria' && !isset($evolucoesPendentes))) ? 'lg:col-span-2' : '' }}">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h2 class="text-lg font-semibold text-gray-900">Atendimentos de Hoje</h2>
                            </div>
                            <a href="{{ route('app.agenda') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                Ver agenda →
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-gray-100">
                            @forelse($atendimentosHoje as $atendimento)
                                <li class="py-3 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $atendimento->data_hora_inicio->format('H:i') }} - {{ $atendimento->paciente->nome_completo ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $atendimento->profissional->name ?? 'N/A' }} • {{ $atendimento->sala->nome ?? 'Sem sala' }} • {{ $atendimento->status }}
                                            </p>
                                        </div>
                                        <a href="{{ route('app.agenda') }}" 
                                           class="ml-4 text-xs text-indigo-600 hover:text-indigo-800">
                                            Ver →
                                        </a>
                                    </div>
                                </li>
                            @empty
                                <li class="py-8 text-center text-gray-500">
                                    <p>Nenhum atendimento hoje</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

