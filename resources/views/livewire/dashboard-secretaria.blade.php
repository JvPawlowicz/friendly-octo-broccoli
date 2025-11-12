<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Secretaria</h1>
                <p class="mt-2 text-sm text-gray-600">Visão geral das atividades administrativas</p>
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
                            <p class="text-yellow-100 text-sm font-medium">Pacientes Aguardando</p>
                            <p class="text-3xl font-bold mt-2">{{ $pacientesAguardando }}</p>
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
                            <p class="text-3xl font-bold mt-2">{{ $documentosPendentes }}</p>
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
                            <p class="text-3xl font-bold mt-2">{{ $totalPacientes }}</p>
                            <p class="text-green-100 text-xs mt-1">+{{ $pacientesNovosMes }} este mês</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atendimentos de Hoje -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mb-6">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-900">Atendimentos de Hoje</h2>
                        </div>
                        <a href="{{ route('app.agenda') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Ver agenda completa →
                        </a>
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
                                                <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    {{ $at->profissional->name ?? 'N/A' }}
                                                    @if($at->sala)
                                                        <span class="ml-2">• {{ $at->sala->nome }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @switch($at->status)
                                            @case('Agendado') bg-gray-100 text-gray-800 @break
                                            @case('Confirmado') bg-blue-100 text-blue-800 @break
                                            @case('Check-in') bg-green-100 text-green-800 @break
                                            @case('Concluído') bg-purple-100 text-purple-800 @break
                                            @case('Cancelado') bg-red-100 text-red-800 @break
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

            <!-- Ações Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('app.pacientes.create') }}" 
                   class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Novo Paciente</h3>
                            <p class="text-xs text-gray-500 mt-1">Cadastrar novo paciente</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('app.planos-saude') }}" 
                   class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Planos de Saúde</h3>
                            <p class="text-xs text-gray-500 mt-1">Gerenciar planos</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('app.pacientes') }}" 
                   class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Lista de Pacientes</h3>
                            <p class="text-xs text-gray-500 mt-1">Ver todos os pacientes</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

