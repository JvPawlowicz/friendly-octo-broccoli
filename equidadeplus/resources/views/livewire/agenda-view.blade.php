<div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Agenda Integrada</h2>
                        <p class="mt-1 text-sm text-gray-500">Gerencie atendimentos, bloqueios e disponibilidade em tempo real.</p>
                    </div>
                    <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Agendados</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $metricas['agendados'] ?? 0 }}</dd>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Confirmados</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $metricas['confirmados'] ?? 0 }}</dd>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Check-in</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $metricas['checkin'] ?? 0 }}</dd>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Cancelados</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $metricas['cancelados'] ?? 0 }}</dd>
                        </div>
                    </dl>
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
                            <select wire:model="favoriteSelecionado"
                                    wire:change="aplicarFavorito($event.target.value)"
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

                    <button wire:click="abrirModal" class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow hover:from-indigo-500 hover:to-purple-500 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Novo atendimento
                    </button>
                    <button wire:click="$refresh" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581m0 0a8.001 8.001 0 01-15.317 0m15.317 0A8.001 8.001 0 004.582 9"></path>
                        </svg>
                        Atualizar
                    </button>
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
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4v10"></path>
                                </svg>
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
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"></path>
                                </svg>
                            </div>
                            <select wire:model.live="userId" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Todos os profissionais</option>
                                    @foreach($profissionais as $prof)
                                        <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        <div class="p-4 bg-purple-50 rounded-xl border border-purple-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-purple-700">Sala</span>
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path>
                                </svg>
                            </div>
                            <select wire:model.live="salaId" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-purple-500 focus:ring-purple-500" {{ !$unidadeId ? 'disabled' : '' }}>
                                <option value="">Todas as salas</option>
                                    @foreach($salas as $sala)
                                        <option value="{{ $sala->id }}">{{ $sala->nome }}</option>
                                    @endforeach
                                </select>
                            @if(!$unidadeId)
                                <p class="mt-2 text-xs text-purple-500">Selecione uma unidade para filtrar salas.</p>
                            @endif
                        </div>

                        <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-amber-700">Status</span>
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 5H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <select wire:model.live="statusFiltro" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500">
                                <option value="">Todos os status</option>
                                @foreach(['Agendado','Confirmado','Check-in','Concluído','Cancelado'] as $statusOption)
                                    <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-amber-500">A cor do evento representa o status atual.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agenda + Legenda -->
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                        <div class="flex flex-wrap items-center gap-3 text-xs font-medium text-gray-500">
                            <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span>Agendado</span>
                            <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span>Confirmado</span>
                            <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-500"></span>Check-in</span>
                            <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-indigo-500"></span>Concluído</span>
                            <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span>Cancelado</span>
                        </div>
                        <div class="text-xs text-gray-400">
                            Arraste para reagendar • Clique para ver detalhes • Atualização em tempo real
                        </div>
                    </div>
                    <div id="calendar" wire:ignore class="calendar-agenda rounded-xl border border-gray-100"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="agenda-toast-container" class="fixed top-20 right-6 z-50 space-y-2 pointer-events-none"></div>

    <!-- Painel de detalhes -->
    @if($mostrarPainelDetalhe && $atendimentoSelecionado)
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40" wire:click="fecharPainelDetalhe"></div>
        <div class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl z-50 flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">Resumo do atendimento</p>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $atendimentoSelecionado['paciente']['nome'] }}</h3>
                </div>
                <button wire:click="fecharPainelDetalhe" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold" style="background-color: {{ $atendimentoSelecionado['status_cor'] }}1A; color: {{ $atendimentoSelecionado['status_cor'] }};">
                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $atendimentoSelecionado['status_cor'] }};"></span>
                        {{ $atendimentoSelecionado['status'] }}
                    </span>
                    <button wire:click="abrirModalStatus({{ $atendimentoSelecionado['id'] }})" class="text-xs text-indigo-600 hover:underline">Alterar status</button>
                    <button wire:click="abrirModal({{ $atendimentoSelecionado['id'] }})" class="text-xs text-gray-500 hover:underline">Editar</button>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 space-y-3 text-sm text-gray-700">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Horário</p>
                        <p class="font-medium">{{ $atendimentoSelecionado['inicio'] }}</p>
                        <p class="text-xs text-gray-500">Duração aproximada de {{ $atendimentoSelecionado['duracao'] }} min</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Término</p>
                        <p class="font-medium">{{ $atendimentoSelecionado['fim'] }}</p>
                    </div>
                </div>

                <div class="space-y-4 text-sm text-gray-700">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Profissional</p>
                        <p class="font-medium">{{ $atendimentoSelecionado['profissional']['nome'] }}</p>
                        <p class="text-xs text-gray-500">{{ $atendimentoSelecionado['profissional']['email'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Paciente</p>
                        <p class="font-medium">{{ $atendimentoSelecionado['paciente']['nome'] }}</p>
                        <p class="text-xs text-gray-500">{{ $atendimentoSelecionado['paciente']['telefone'] ?: 'Telefone não informado' }}</p>
                        <p class="text-xs text-gray-500">{{ $atendimentoSelecionado['paciente']['email'] ?: 'E-mail não informado' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white rounded-lg border border-gray-100 p-3">
                            <p class="text-xs uppercase tracking-wide text-gray-400">Sala</p>
                            <p class="font-medium text-gray-700">{{ $atendimentoSelecionado['sala'] ?? 'Não informada' }}</p>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-100 p-3">
                            <p class="text-xs uppercase tracking-wide text-gray-400">Unidade</p>
                            <p class="font-medium text-gray-700">{{ $atendimentoSelecionado['unidade'] ?? 'Não informada' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 space-y-3 text-sm">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Ações rápidas</p>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="abrirModal({{ $atendimentoSelecionado['id'] }})" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100">Editar atendimento</button>
                    <button wire:click="abrirModalStatus({{ $atendimentoSelecionado['id'] }})" class="px-3 py-1.5 rounded-lg border border-indigo-200 text-indigo-600 hover:bg-indigo-50">Atualizar status</button>
                    <button wire:click="abrirModal(null, null)" class="px-3 py-1.5 rounded-lg border border-purple-200 text-purple-600 hover:bg-purple-50">Criar novo</button>
                </div>
            </div>
        </div>
    @endif

        <!-- Modal FormAtendimento -->
        @if($mostrarModal)
        <div class="fixed inset-0 bg-gray-800/40 backdrop-blur-sm z-50 flex items-start justify-center py-10" wire:click.self="fecharModal">
            <div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $atendimentoId ? 'Editar atendimento' : 'Novo atendimento' }}</h3>
                        <button wire:click="fecharModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                <div class="p-6 bg-gray-50">
                    @livewire('form-atendimento', ['atendimentoId' => $atendimentoId], key('form-atendimento-' . ($atendimentoId ?? 'new')))
                </div>
                </div>
            </div>
        @endif

    <!-- Modal Gerenciar Status -->
        @if($mostrarModalStatus)
        <div class="fixed inset-0 bg-gray-800/40 backdrop-blur-sm z-50 flex items-start justify-center py-20" wire:click.self="fecharModalStatus">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Gerenciar status</h3>
                        <button wire:click="fecharModalStatus" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                <div class="p-6 bg-gray-50">
                    @livewire('gerir-status-atendimento', ['atendimentoId' => $atendimentoIdStatus], key('gerir-status-' . $atendimentoIdStatus))
                </div>
                </div>
            </div>
        @endif

    @push('scripts')
    <script>
            document.addEventListener('livewire:load', () => {
                const initialEvents = @js($eventos);
                const initialChannels = @js($canaisAgenda);
                initAgendaCalendar(initialEvents, initialChannels);
            });

            function initAgendaCalendar(initialEvents = [], canais = []) {
                const calendarEl = document.getElementById('calendar');
                if (!calendarEl) return;

                if (window.agendaCalendar) {
                    window.agendaCalendar.destroy();
                }

                const calendar = new window.Calendar(calendarEl, {
                plugins: [window.dayGridPlugin, window.timeGridPlugin, window.interactionPlugin],
                    initialView: localStorage.getItem('agenda:lastView') || 'timeGridWeek',
                locale: 'pt-br',
                    firstDay: 1,
                    nowIndicator: true,
                    slotMinTime: '07:00:00',
                    slotMaxTime: '21:00:00',
                    expandRows: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                    eventTimeFormat: { hour: '2-digit', minute: '2-digit' },
                    events: initialEvents,
                    eventDidMount: function(info) {
                        if (info.event.extendedProps.tipo === 'atendimento') {
                            info.el.setAttribute('title', `${info.event.extendedProps.paciente} • ${info.event.extendedProps.profissional}`);
                        }
                    },
                eventClick: function(info) {
                    if (info.event.extendedProps.tipo === 'atendimento') {
                            info.jsEvent.preventDefault();
                            @this.call('mostrarResumo', info.event.extendedProps.atendimento_id);
                    }
                },
                dateClick: function(info) {
                        const horaPadrao = 'T09:00';
                        @this.call('abrirModal', null, info.dateStr + horaPadrao);
                },
                eventDrop: function(info) {
                        if (info.event.extendedProps.tipo !== 'atendimento') return;
                        const id = info.event.extendedProps.atendimento_id;
                        const novoInicio = info.event.start.toISOString();
                        @this.call('atualizarDataAtendimento', id, novoInicio).then((response) => {
                            if (response && response.conflict) {
                                info.revert();
                                return;
                            }
                            agendaShowToast('Atendimento reagendado com sucesso!', 'success');
                        });
                },
                eventResize: function(info) {
                        if (info.event.extendedProps.tipo !== 'atendimento') return;
                        const id = info.event.extendedProps.atendimento_id;
                        const novoInicio = info.event.start.toISOString();
                        const novoFim = info.event.end.toISOString();
                        @this.call('atualizarDuracaoAtendimento', id, novoInicio, novoFim).then((response) => {
                            if (response && response.conflict) {
                                info.revert();
                                return;
                            }
                            agendaShowToast('Duração atualizada!', 'success');
                        });
                },
                editable: true,
                    droppable: false,
                    selectable: true,
                });

                calendar.on('viewDidMount', function(arg) {
                    localStorage.setItem('agenda:lastView', arg.view.type);
                });

                window.agendaCalendar = calendar;
            calendar.render();

                subscribeAgendaChannels(canais);
            }

            function updateAgendaEvents(eventos = []) {
                if (!window.agendaCalendar) return;
                window.agendaCalendar.batchRendering(() => {
                    window.agendaCalendar.removeAllEvents();
                    eventos.forEach(evento => window.agendaCalendar.addEvent(evento));
                });
            }

            function subscribeAgendaChannels(canais = []) {
                if (typeof Echo === 'undefined') return;
                window.agendaEchoSubscriptions = window.agendaEchoSubscriptions || {};

                // Remove canais que não são mais necessários
                Object.keys(window.agendaEchoSubscriptions).forEach((canal) => {
                    if (!canais.includes(parseInt(canal))) {
                        Echo.leave(`private-agenda.${canal}`);
                        delete window.agendaEchoSubscriptions[canal];
                    }
                });

                // Adiciona novos canais
                canais.forEach((canalId) => {
                    const key = canalId.toString();
                    if (window.agendaEchoSubscriptions[key]) return;

                    window.agendaEchoSubscriptions[key] = Echo.private('agenda.' + key)
                        .listen('.AtendimentoAtualizado', () => {
                        @this.call('atualizarAgenda');
                        });
                });
            }

            function agendaShowToast(message, type = 'info') {
                const container = document.getElementById('agenda-toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = `pointer-events-auto flex items-center gap-2 px-4 py-2 rounded-xl border shadow transition opacity-100 bg-white ${type === 'error' ? 'border-red-200 text-red-600' : type === 'success' ? 'border-green-200 text-green-600' : 'border-indigo-200 text-indigo-600'}`;
                toast.innerHTML = `<span>${message}</span>`;

                container.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('opacity-0');
                    toast.classList.add('translate-y-1');
                    setTimeout(() => toast.remove(), 250);
                }, 3500);
            }

            Livewire.on('calendar-update', ({ eventos }) => updateAgendaEvents(eventos));
            Livewire.on('agenda-canais', ({ canais }) => subscribeAgendaChannels(canais));
            Livewire.on('agenda-conflito', ({ message }) => agendaShowToast(message, 'error'));
            Livewire.on('atendimento-salvo', () => agendaShowToast('Agenda atualizada com sucesso!', 'success'));
    </script>
    @endpush
</div>
