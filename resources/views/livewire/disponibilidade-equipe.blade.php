<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">Disponibilidade da Equipe</h1>
                            <p class="text-gray-600">Visualize a disponibilidade de todos os profissionais e gerencie sua própria disponibilidade</p>
                        </div>
                        <button wire:click="abrirModal" 
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar Minha Disponibilidade
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Profissional</label>
                            <select wire:model.live="profissionalFiltro" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os profissionais</option>
                                @foreach($profissionais as $prof)
                                    <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Unidade</label>
                            <select wire:model.live="unidadeFiltro" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas as unidades</option>
                                @foreach($unidades as $unidade)
                                    <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button wire:click="$set('profissionalFiltro', null); $set('unidadeFiltro', null)" 
                                    class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                                Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Minha Disponibilidade (Admin) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Minha Disponibilidade</h2>
                    @php
                        $minhasDisponibilidades = $minhasDisponibilidades;
                    @endphp
                    @if($minhasDisponibilidades->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <p>Você ainda não cadastrou sua disponibilidade.</p>
                            <p class="text-sm mt-2">Clique em "Adicionar Minha Disponibilidade" para começar.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach([0, 1, 2, 3, 4, 5, 6] as $dia)
                                @php
                                    $disponibilidadesDia = $minhasDisponibilidades->get($dia, collect());
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ $this->getDiaSemanaNome($dia) }}</h3>
                                    @if($disponibilidadesDia->isEmpty())
                                        <p class="text-xs text-gray-400">Sem disponibilidade</p>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($disponibilidadesDia as $disp)
                                                <div class="flex justify-between items-center p-2 bg-indigo-50 rounded">
                                                    <span class="text-xs font-medium text-indigo-900">
                                                        {{ \Carbon\Carbon::parse($disp->hora_inicio)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($disp->hora_fim)->format('H:i') }}
                                                    </span>
                                                    <div class="flex space-x-1">
                                                        <button wire:click="abrirModal({{ $disp->id }})"
                                                                class="text-indigo-600 hover:text-indigo-800">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                        <button wire:click="deletar({{ $disp->id }})"
                                                                onclick="return confirm('Tem certeza?')"
                                                                class="text-red-600 hover:text-red-800">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Matriz de Disponibilidade da Equipe -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Disponibilidade da Equipe</h2>
                    
                    @php
                        $disponibilidadesEquipe = $disponibilidadesEquipe;
                        $diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
                    @endphp

                    @if($disponibilidadesEquipe->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Nenhuma disponibilidade cadastrada pela equipe.</p>
                        </div>
                    @else
                        <!-- Tabela de Disponibilidade -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profissional</th>
                                        @foreach([1, 2, 3, 4, 5, 6, 0] as $dia)
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ substr($diasSemana[$dia], 0, 3) }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($profissionais as $prof)
                                        @php
                                            $diasProf = $disponibilidadesEquipe->get($prof->id, collect());
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $prof->name }}</div>
                                                @if($prof->hasRole('Admin'))
                                                    <span class="text-xs text-indigo-600">Admin</span>
                                                @endif
                                            </td>
                                            @foreach([1, 2, 3, 4, 5, 6, 0] as $dia)
                                                @php
                                                    $dispsDia = $diasProf->get($dia, collect());
                                                @endphp
                                                <td class="px-4 py-3 text-center">
                                                    @if($dispsDia->isEmpty())
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            ❌ Indisponível
                                                        </span>
                                                    @else
                                                        <div class="space-y-1">
                                                            @foreach($dispsDia as $disp)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    ✅ {{ \Carbon\Carbon::parse($disp->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($disp->hora_fim)->format('H:i') }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Horários Livres (Admin) -->
                        @if(isset($horariosLivres) && !empty($horariosLivres))
                        <div class="mt-6 bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Horários Livres (Sem Agendamento)</h3>
                            <div class="space-y-4">
                                @foreach($profissionais as $prof)
                                    @php
                                        $livresProf = $horariosLivres[$prof->id] ?? [];
                                    @endphp
                                    @if(!empty($livresProf))
                                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                                            <h4 class="font-medium text-gray-900 mb-3">{{ $prof->name }}</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-7 gap-3">
                                                @foreach([1, 2, 3, 4, 5, 6, 0] as $dia)
                                                    @php
                                                        $livresDia = $livresProf[$dia] ?? [];
                                                    @endphp
                                                    <div>
                                                        <p class="text-xs font-medium text-gray-700 mb-2">{{ substr($diasSemana[$dia], 0, 3) }}</p>
                                                        @if(empty($livresDia))
                                                            <p class="text-xs text-gray-400">Sem horários livres</p>
                                                        @else
                                                            <div class="space-y-1">
                                                                @foreach(array_slice($livresDia, 0, 3) as $livre)
                                                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                                                        {{ $livre['inicio'] }}-{{ $livre['fim'] }}
                                                                    </span>
                                                                @endforeach
                                                                @if(count($livresDia) > 3)
                                                                    <p class="text-xs text-gray-500">+{{ count($livresDia) - 3 }} mais</p>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Estatísticas de Cobertura -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-7 gap-4">
                            @foreach([1, 2, 3, 4, 5, 6, 0] as $dia)
                                @php
                                    $coberturaDia = $cobertura[$dia] ?? [];
                                    $totalSlots = count($coberturaDia);
                                    $slotsComCobertura = 0;
                                    if ($totalSlots > 0) {
                                        foreach ($coberturaDia as $slot => $profissionais) {
                                            if (count($profissionais) > 0) {
                                                $slotsComCobertura++;
                                            }
                                        }
                                        $percentual = round(($slotsComCobertura / $totalSlots) * 100);
                                    } else {
                                        $percentual = 0;
                                    }
                                @endphp
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ substr($diasSemana[$dia], 0, 3) }}</h4>
                                    <div class="text-2xl font-bold text-indigo-600">{{ $percentual }}%</div>
                                    <p class="text-xs text-gray-500 mt-1">Cobertura</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Adicionar/Editar Disponibilidade -->
    @if($mostrarModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="fecharModal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $disponibilidadeId ? 'Editar' : 'Adicionar' }} Minha Disponibilidade
                    </h3>
                    <button wire:click="fecharModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="salvar">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dia da Semana *</label>
                        <select wire:model="dia_da_semana" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Selecione um dia</option>
                            <option value="0">Domingo</option>
                            <option value="1">Segunda-feira</option>
                            <option value="2">Terça-feira</option>
                            <option value="3">Quarta-feira</option>
                            <option value="4">Quinta-feira</option>
                            <option value="5">Sexta-feira</option>
                            <option value="6">Sábado</option>
                        </select>
                        @error('dia_da_semana') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Horário de Início *</label>
                        <input type="time" wire:model="hora_inicio" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('hora_inicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Horário de Término *</label>
                        <input type="time" wire:model="hora_fim" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('hora_fim') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="fecharModal" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:from-indigo-700 hover:to-purple-700 transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

