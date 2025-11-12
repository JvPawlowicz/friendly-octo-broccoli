<div class="relative">
    <div class="relative">
        <input type="text" 
               wire:model.live.debounce.300ms="query"
               wire:focus="buscar"
               placeholder="Buscar pacientes, atendimentos, evoluções..."
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    @if($mostrarResultados && strlen($query) >= 2)
    <div class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto">
        <div class="p-4">
            @php
                $totalResultados = $resultados['pacientes']->count() + 
                                  $resultados['atendimentos']->count() + 
                                  $resultados['evolucoes']->count() + 
                                  $resultados['avaliacoes']->count();
            @endphp

            @if($totalResultados === 0)
                <p class="text-sm text-gray-500 text-center py-4">Nenhum resultado encontrado</p>
            @else
                <!-- Pacientes -->
                @if($resultados['pacientes']->isNotEmpty())
                <div class="mb-4">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Pacientes</h4>
                    <div class="space-y-2">
                        @foreach($resultados['pacientes'] as $paciente)
                            <a href="{{ route('app.pacientes.prontuario', $paciente->id) }}" 
                               wire:click="fecharResultados"
                               class="block p-2 hover:bg-gray-50 rounded-lg transition">
                                <p class="text-sm font-medium text-gray-900">{{ $paciente->nome_completo }}</p>
                                <p class="text-xs text-gray-500">{{ $paciente->cpf ?? 'Sem CPF' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Atendimentos -->
                @if($resultados['atendimentos']->isNotEmpty())
                <div class="mb-4">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Atendimentos</h4>
                    <div class="space-y-2">
                        @foreach($resultados['atendimentos'] as $atendimento)
                            <a href="{{ route('app.agenda') }}" 
                               wire:click="fecharResultados"
                               class="block p-2 hover:bg-gray-50 rounded-lg transition">
                                <p class="text-sm font-medium text-gray-900">{{ $atendimento->paciente->nome_completo }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $atendimento->data_hora_inicio->format('d/m/Y H:i') }} • {{ $atendimento->profissional->name ?? 'N/A' }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Evoluções -->
                @if($resultados['evolucoes']->isNotEmpty())
                <div class="mb-4">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Evoluções</h4>
                    <div class="space-y-2">
                        @foreach($resultados['evolucoes'] as $evolucao)
                            <a href="{{ route('app.pacientes.prontuario', $evolucao->paciente_id) }}" 
                               wire:click="fecharResultados"
                               class="block p-2 hover:bg-gray-50 rounded-lg transition">
                                <p class="text-sm font-medium text-gray-900">{{ $evolucao->paciente->nome_completo }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ \Illuminate\Support\Str::limit($evolucao->relato_clinico, 50) }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Avaliações -->
                @if($resultados['avaliacoes']->isNotEmpty())
                <div class="mb-4">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Avaliações</h4>
                    <div class="space-y-2">
                        @foreach($resultados['avaliacoes'] as $avaliacao)
                            <a href="{{ route('app.pacientes.prontuario', $avaliacao->paciente_id) }}" 
                               wire:click="fecharResultados"
                               class="block p-2 hover:bg-gray-50 rounded-lg transition">
                                <p class="text-sm font-medium text-gray-900">{{ $avaliacao->template->nome_template }}</p>
                                <p class="text-xs text-gray-500">{{ $avaliacao->paciente->nome_completo }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    @endif
</div>

