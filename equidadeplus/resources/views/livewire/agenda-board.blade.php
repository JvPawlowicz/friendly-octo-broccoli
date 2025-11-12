<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Agenda • Board diário</h1>
                <p class="text-sm text-slate-500">Arrume o dia rapidamente mudando status dos atendimentos.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <button wire:click="diaAnterior" class="inline-flex items-center px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <input type="date" wire:model.live="dataConsulta"
                           class="rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button wire:click="proximoDia" class="inline-flex items-center px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button wire:click="hoje" class="inline-flex items-center px-3 py-2 rounded-lg border border-indigo-200 text-sm font-medium text-indigo-600 hover:bg-indigo-50">
                        Hoje
                    </button>
                </div>

                @if($unidades->count() > 1 || (auth()->user()->hasRole('Admin') && $unidades->count() > 0))
                    <div>
                        <select wire:model="unidadeId" class="rounded-lg border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 min-w-[180px]">
                            <option value="">Todas as unidades</option>
                            @foreach($unidades as $unidade)
                                <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <a href="{{ route('app.agenda') }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-100">
                    Voltar para agenda
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 xl:grid-cols-5">
            @foreach($colunas as $status => $cards)
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ $status }}</p>
                            <p class="text-xs text-slate-400">{{ $metricas[$status] ?? 0 }} atendimento(s)</p>
                        </div>
                        @php
                            $cores = [
                                'Agendado' => 'bg-blue-100 text-blue-700',
                                'Confirmado' => 'bg-emerald-100 text-emerald-700',
                                'Check-in' => 'bg-amber-100 text-amber-700',
                                'Concluído' => 'bg-indigo-100 text-indigo-700',
                                'Cancelado' => 'bg-rose-100 text-rose-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cores[$status] ?? 'bg-slate-100 text-slate-600' }}">{{ $metricas[$status] ?? 0 }}</span>
                    </div>

                    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-3 max-h-[600px]">
                        @forelse($cards as $card)
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-slate-400 tracking-wide">{{ $card['horario'] }}</span>
                                    <span class="text-xs text-slate-400">{{ $card['sala'] ?? 'Sem sala' }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $card['paciente'] }}</p>
                                    <p class="text-xs text-slate-500">com {{ $card['profissional'] }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @foreach($statusOrdenacao as $destino)
                                        @if($destino !== $card['status'])
                                            <button type="button"
                                                    wire:click="moverStatus({{ $card['id'] }}, '{{ $destino }}')"
                                                    class="px-2 py-1 text-xs rounded-lg border {{ $destino === 'Cancelado' ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : ($destino === 'Concluído' ? 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' : 'border-slate-200 text-slate-600 hover:bg-slate-50') }}">
                                                {{ $destino }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-400 text-center py-6 border border-dashed border-slate-200 rounded-xl">
                                Nenhum atendimento neste status.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
