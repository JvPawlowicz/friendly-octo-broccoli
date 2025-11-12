<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Relatório de Frequência</h2>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data Início</label>
                            <input type="date" wire:model="dataInicio" wire:change="gerarRelatorio" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data Fim</label>
                            <input type="date" wire:model="dataFim" wire:change="gerarRelatorio" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Profissional</label>
                            <select wire:model="profissionalId" wire:change="gerarRelatorio" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos</option>
                                @foreach($profissionais as $prof)
                                    <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Paciente</label>
                            <select wire:model="pacienteId" wire:change="gerarRelatorio" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id }}">{{ $paciente->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Concluídos</p>
                            <p class="text-2xl font-bold text-green-600">{{ $totalConcluidos }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Cancelados</p>
                            <p class="text-2xl font-bold text-red-600">{{ $totalCancelados }}</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Taxa de Presença</p>
                            <p class="text-2xl font-bold text-blue-600">
                                {{ $totalConcluidos + $totalCancelados > 0 ? round(($totalConcluidos / ($totalConcluidos + $totalCancelados)) * 100, 2) : 0 }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Detalhamento por Paciente</h3>
                        <button wire:click="exportar" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Exportar CSV
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concluídos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cancelados</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taxa Presença</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($dados as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item['paciente']->nome_completo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item['total'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-green-600">{{ $item['concluidos'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-red-600">{{ $item['cancelados'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item['taxa_presenca'] }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum dado encontrado</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
