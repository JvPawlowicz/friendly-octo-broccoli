<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Relatório de Produtividade</h2>
                    <p class="mt-1 text-sm text-gray-500">Acompanhe desempenho, ocupação de salas e absenteísmo.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model.live="favoriteName" placeholder="Nome do filtro"
                               class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               maxlength="60">
                        <button type="button" wire:click="salvarFavoritoAtual"
                                class="inline-flex items-center px-3 py-2 border border-indigo-200 text-sm font-medium text-indigo-600 rounded-lg hover:bg-indigo-50 transition">
                            Salvar filtro
                        </button>
                    </div>
                    @if(!empty($favoritos))
                        <div class="flex items-center gap-2">
                            <select wire:model="favoriteSelecionado" wire:change="aplicarFavorito($event.target.value)"
                                    class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Favoritos</option>
                                @foreach($favoritos as $favorito)
                                    <option value="{{ $favorito['id'] }}">{{ $favorito['name'] }}</option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="excluirFavorito($favoriteSelecionado)" @if(!$favoriteSelecionado) disabled @endif
                                    class="inline-flex items-center px-3 py-2 border border-red-200 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition disabled:opacity-50">
                                Remover
                            </button>
                        </div>
                    @endif
                    <button wire:click="exportarCsv" class="inline-flex items-center px-4 py-2 rounded-lg border border-indigo-200 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v8m0 0l3-3m-3 3l-3-3m9-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v6"></path>
                        </svg>
                        Exportar CSV
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Início</label>
                        <input type="date" wire:model="dataInicio" wire:change="gerarRelatorio" class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Fim</label>
                        <input type="date" wire:model="dataFim" wire:change="gerarRelatorio" class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Profissional</label>
                        <select wire:model="profissionalId" wire:change="gerarRelatorio" class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Todos</option>
                            @foreach($profissionais as $prof)
                                <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Unidade</label>
                        <select wire:model="unidadeId" wire:change="gerarRelatorio" class="mt-1 w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Todas</option>
                            @foreach($unidades as $unidade)
                                <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Cards resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-5 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl text-white shadow-lg">
                    <p class="text-sm text-indigo-100">Atendimentos concluídos</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalAtendimentos }}</p>
                    <p class="mt-1 text-xs text-indigo-100">Período {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} • {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</p>
                </div>
                <div class="p-5 bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl text-white shadow-lg">
                    <p class="text-sm text-rose-100">Cancelados / Ausências</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalCancelados }}</p>
                    <p class="mt-1 text-xs text-rose-100">{{ $percentualAbsenteismo }}% do total agendado</p>
                </div>
                <div class="p-5 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl text-white shadow-lg">
                    <p class="text-sm text-emerald-100">Profissionais ativos no período</p>
                    <p class="text-3xl font-bold mt-2">{{ count($dados) }}</p>
                    <p class="mt-1 text-xs text-emerald-100">Com atendimentos concluídos</p>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Produtividade por profissional</h3>
                            <span class="text-xs text-gray-400">Top {{ count($serieProdutividade['labels']) }} profissionais</span>
                        </div>
                        <canvas id="chartProdutividade" height="220"></canvas>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                    <div class="p-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Absenteísmo</h3>
                        <canvas id="chartAbsenteismo" height="200"></canvas>
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Cancelados</span>
                            <span class="font-semibold text-rose-600">{{ $totalCancelados }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Realizados</span>
                            <span class="font-semibold text-emerald-600">{{ $serieAbsenteismo['valores'][0] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ocupação por sala</h3>
                        <span class="text-xs text-gray-400">Eventos registrados no período</span>
                    </div>
                    <canvas id="chartOcupacao" height="220"></canvas>
                </div>
            </div>

            <!-- Tabela -->
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalhamento por profissional</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Profissional</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dias trabalhados</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Média diária</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($dados as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['profissional_nome'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600">{{ $item['total'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item['dias_trabalhados'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format($item['media_diaria'], 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">Nenhum atendimento concluído no período selecionado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" integrity="sha384-7o1ZiZsa1+lZeXqIuAvV38DSHLVYDBlnJrped1IovnHgwlHGawEq+y3OCAXoTr4a" crossorigin="anonymous"></script>
        <script>
            let chartProdutividade;
            let chartOcupacao;
            let chartAbsenteismo;

            document.addEventListener('livewire:load', () => {
                inicializarGraficos(
                    @js($serieProdutividade),
                    @js($serieOcupacao),
                    @js($serieAbsenteismo)
                );
            });

            function inicializarGraficos(produtividade, ocupacao, absenteismo) {
                const ctxProd = document.getElementById('chartProdutividade');
                const ctxOcup = document.getElementById('chartOcupacao');
                const ctxAbs = document.getElementById('chartAbsenteismo');

                if (ctxProd) {
                    chartProdutividade = new Chart(ctxProd, {
                        type: 'bar',
                        data: {
                            labels: produtividade.labels,
                            datasets: [
                                {
                                    label: 'Atendimentos',
                                    data: produtividade.totais,
                                    backgroundColor: '#6366f1',
                                    borderRadius: 12,
                                },
                                {
                                    label: 'Média diária',
                                    data: produtividade.medias,
                                    backgroundColor: '#a855f7',
                                    borderRadius: 12,
                                }
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top', labels: { color: '#4b5563' } },
                                tooltip: { mode: 'index', intersect: false },
                            },
                            scales: {
                                x: { ticks: { color: '#6b7280' } },
                                y: { ticks: { color: '#6b7280' }, beginAtZero: true },
                            },
                        },
                    });
                }

                if (ctxOcup) {
                    chartOcupacao = new Chart(ctxOcup, {
                        type: 'bar',
                        data: {
                            labels: ocupacao.labels,
                            datasets: [
                                {
                                    label: 'Atendimentos por sala',
                                    data: ocupacao.valores,
                                    backgroundColor: '#0ea5e9',
                                    borderRadius: 12,
                                },
                            ],
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { mode: 'nearest', intersect: false },
                            },
                            scales: {
                                x: { ticks: { color: '#6b7280' }, beginAtZero: true },
                                y: { ticks: { color: '#6b7280' } },
                            },
                        },
                    });
                }

                if (ctxAbs) {
                    chartAbsenteismo = new Chart(ctxAbs, {
                        type: 'doughnut',
                        data: {
                            labels: absenteismo.labels,
                            datasets: [
                                {
                                    data: absenteismo.valores,
                                    backgroundColor: ['#22c55e', '#f97316'],
                                    borderWidth: 0,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: '#4b5563', boxWidth: 12 },
                                },
                            },
                        },
                    });
                }
            }

            function atualizarGraficos(produtividade, ocupacao, absenteismo) {
                if (chartProdutividade) {
                    chartProdutividade.data.labels = produtividade.labels;
                    chartProdutividade.data.datasets[0].data = produtividade.totais;
                    chartProdutividade.data.datasets[1].data = produtividade.medias;
                    chartProdutividade.update();
                }

                if (chartOcupacao) {
                    chartOcupacao.data.labels = ocupacao.labels;
                    chartOcupacao.data.datasets[0].data = ocupacao.valores;
                    chartOcupacao.update();
                }

                if (chartAbsenteismo) {
                    chartAbsenteismo.data.datasets[0].data = absenteismo.valores;
                    chartAbsenteismo.update();
                }
            }

            Livewire.on('chart-atualizar', ({ produtividade, ocupacao, absenteismo }) => {
                atualizarGraficos(produtividade, ocupacao, absenteismo);
            });
        </script>
    @endpush
</div>
