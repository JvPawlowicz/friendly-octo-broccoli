<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <x-ui.breadcrumbs :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Agenda']
            ]" />
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Agenda Integrada</h2>
                        <p class="mt-1 text-sm text-gray-500">Gerencie atendimentos, bloqueios e disponibilidade em tempo real.</p>
                    </div>
                    <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Agendados</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $metricasResumo['agendados'] ?? 0 }}</dd>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Confirmados</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $metricasResumo['confirmados'] ?? 0 }}</dd>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Check-in</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $metricasResumo['checkin'] ?? 0 }}</dd>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Cancelados</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $metricasResumo['cancelados'] ?? 0 }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Toggle de Visualização -->
                    <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1">
                        <button wire:click="$set('viewMode', 'calendar')" 
                                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $viewMode === 'calendar' ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Calendário
                        </button>
                        <button wire:click="$set('viewMode', 'board')" 
                                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $viewMode === 'board' ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                            </svg>
                            Board
                        </button>
                    </div>

                    <!-- Favoritos -->
                    @if(!empty($favoritos))
                        <div class="flex items-center gap-2">
                            <select wire:model="favoriteSelecionado"
                                    wire:change="aplicarFavorito($event.target.value)"
                                    class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Favoritos</option>
                                @foreach($favoritos as $favorito)
                                    <option value="{{ $favorito['id'] }}">{{ $favorito['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <x-ui.loading-button 
                        wire:click="abrirModal"
                        loading-text="Abrindo..."
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow hover:from-indigo-500 hover:to-purple-500 transition disabled:opacity-50">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Novo atendimento
                    </x-ui.loading-button>
                    <x-ui.loading-button 
                        wire:click="$refresh"
                        loading-text="Atualizando..."
                        spinner-color="gray"
                        class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-100 transition disabled:opacity-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581m0 0a8.001 8.001 0 01-15.317 0m15.317 0A8.001 8.001 0 004.582 9"></path>
                        </svg>
                        Atualizar
                    </x-ui.loading-button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filtros -->
            <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-indigo-700">Unidade</span>
                            </div>
                            <select wire:model.live="unidadeId" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas as unidades</option>
                                @foreach($unidades as $unidade)
                                    <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-green-700">Profissional</span>
                            </div>
                            <select wire:model.live="userId" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Todos os profissionais</option>
                                @foreach($profissionais as $profissional)
                                    <option value="{{ $profissional->id }}">{{ $profissional->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="p-4 bg-purple-50 rounded-xl border border-purple-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-purple-700">Sala</span>
                            </div>
                            <select wire:model.live="salaId" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-purple-500 focus:ring-purple-500" @if(!$unidadeId) disabled @endif>
                                <option value="">Todas as salas</option>
                                @foreach($salas as $sala)
                                    <option value="{{ $sala->id }}">{{ $sala->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="p-4 bg-orange-50 rounded-xl border border-orange-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-orange-700">Status</span>
                            </div>
                            <select wire:model.live="statusFiltro" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Todos os status</option>
                                <option value="Agendado">Agendado</option>
                                <option value="Confirmado">Confirmado</option>
                                <option value="Check-in">Check-in</option>
                                <option value="Concluído">Concluído</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo: Calendário ou Board -->
            @if($viewMode === 'calendar')
                <!-- Visualização Calendário -->
                <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                    <div class="p-6">
                        <div id="calendar" wire:ignore></div>
                    </div>
                </div>
            @else
                <!-- Visualização Board -->
                <div class="space-y-4">
                    <!-- Controles de Data (Board) -->
                    <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100 p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Board diário</h3>
                            <div class="flex items-center gap-2">
                                <button wire:click="diaAnterior" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <input type="date" wire:model.live="dataConsulta"
                                       class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button wire:click="proximoDia" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <button wire:click="hoje" class="inline-flex items-center px-3 py-2 rounded-lg border border-indigo-200 text-sm font-medium text-indigo-600 hover:bg-indigo-50">
                                    Hoje
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Colunas do Board -->
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 xl:grid-cols-5">
                        @foreach($statusOrdenacao as $status)
                            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm flex flex-col">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">{{ $status }}</p>
                                        <p class="text-xs text-gray-400">{{ $metricas[$status] ?? 0 }} atendimento(s)</p>
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
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cores[$status] ?? 'bg-gray-100 text-gray-600' }}">{{ $metricas[$status] ?? 0 }}</span>
                                </div>

                                <div class="flex-1 overflow-y-auto px-3 py-4 space-y-3 max-h-[600px]">
                                    @forelse($colunas[$status] ?? [] as $card)
                                        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-4 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-medium text-gray-400 tracking-wide">{{ $card['horario'] }}</span>
                                                <span class="text-xs text-gray-400">{{ $card['sala'] ?? 'Sem sala' }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800">{{ $card['paciente'] }}</p>
                                                <p class="text-xs text-gray-500">com {{ $card['profissional'] }}</p>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                @foreach($statusOrdenacao as $destino)
                                                    @if($destino !== $card['status'])
                                                        <button type="button"
                                                                wire:click="moverStatus({{ $card['id'] }}, '{{ $destino }}')"
                                                                class="px-2 py-1 text-xs rounded-lg border {{ $destino === 'Cancelado' ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : ($destino === 'Concluído' ? 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' : 'border-gray-200 text-gray-600 hover:bg-gray-50') }}">
                                                            {{ $destino }}
                                                        </button>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-400 text-center py-6 border border-dashed border-gray-200 rounded-xl">
                                            Nenhum atendimento neste status.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Atendimento -->
    @if($mostrarModal)
        <div x-data="{ show: @entangle('mostrarModal') }" 
             x-show="show" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                     @click="show = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $atendimentoId ? 'Editar Atendimento' : 'Novo Atendimento' }}
                            </h3>
                            <button @click="show = false" 
                                    wire:click="fecharModal"
                                    class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div>
                            @livewire('form-atendimento', [
                                'atendimentoId' => $atendimentoId,
                                'dataInicio' => $dataInicioModal,
                                'pacienteId' => null,
                                'userId' => null,
                                'salaId' => null
                            ], key('atendimento-' . ($atendimentoId ?? 'new-' . time())))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
@if($viewMode === 'calendar')
<script>
    (function() {
        let calendarInstance = null;
        
        function getAgendaComponent() {
            if (!window.Livewire) return null;
            // Encontrar o componente Agenda pelo elemento mais próximo
            const agendaElement = document.querySelector('[wire\\:id]');
            if (agendaElement) {
                const wireId = agendaElement.getAttribute('wire:id');
                return window.Livewire.find(wireId);
            }
            return null;
        }
        
        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl || !window.initAgendaCalendar) return;
            
            const eventos = @json($eventos);
            calendarInstance = window.initAgendaCalendar(eventos);
            
            if (calendarInstance) {
                calendarInstance.setOption('dateClick', function(info) {
                    // Usar evento Livewire
                    Livewire.dispatch('abrir-modal-agenda', { dataInicio: info.dateStr });
                });
                
                calendarInstance.setOption('eventClick', function(info) {
                    const atendimentoId = info.event.extendedProps?.atendimento_id;
                    if (atendimentoId) {
                        // Usar evento Livewire
                        Livewire.dispatch('abrir-modal-agenda', { atendimentoId: atendimentoId });
                    }
                });
            }
        }
        
        // Aguardar Livewire estar pronto
        if (window.Livewire) {
            // Livewire já está carregado
            setTimeout(initCalendar, 300);
        } else {
            // Aguardar Livewire carregar
            document.addEventListener('livewire:init', function() {
                setTimeout(initCalendar, 300);
            });
        }
        
        // Escutar atualizações de eventos
        if (window.Livewire) {
            Livewire.on('calendar-update', (data) => {
                if (window.initAgendaCalendar && data.eventos) {
                    // Destruir instância anterior
                    if (calendarInstance) {
                        calendarInstance.destroy();
                    }
                    
                    // Criar nova instância com eventos atualizados
                    calendarInstance = window.initAgendaCalendar(data.eventos);
                    
                    if (calendarInstance) {
                        calendarInstance.setOption('dateClick', function(info) {
                            Livewire.dispatch('abrir-modal-agenda', { dataInicio: info.dateStr });
                        });
                        
                        calendarInstance.setOption('eventClick', function(info) {
                            const atendimentoId = info.event.extendedProps?.atendimento_id;
                            if (atendimentoId) {
                                Livewire.dispatch('abrir-modal-agenda', { atendimentoId: atendimentoId });
                            }
                        });
                    }
                }
            });
        } else {
            document.addEventListener('livewire:init', function() {
                Livewire.on('calendar-update', (data) => {
                    if (window.initAgendaCalendar && data.eventos) {
                        if (calendarInstance) {
                            calendarInstance.destroy();
                        }
                        calendarInstance = window.initAgendaCalendar(data.eventos);
                        
                        if (calendarInstance) {
                            calendarInstance.setOption('dateClick', function(info) {
                                Livewire.dispatch('abrir-modal-agenda', { dataInicio: info.dateStr });
                            });
                            
                            calendarInstance.setOption('eventClick', function(info) {
                                const atendimentoId = info.event.extendedProps?.atendimento_id;
                                if (atendimentoId) {
                                    Livewire.dispatch('abrir-modal-agenda', { atendimentoId: atendimentoId });
                                }
                            });
                        }
                    }
                });
            });
        }
    })();
</script>
@endif
@endpush

