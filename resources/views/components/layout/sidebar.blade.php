<aside class="hidden lg:flex lg:w-72 lg:flex-col lg:fixed lg:inset-y-0 bg-white border-r border-slate-200">
    <div class="flex grow flex-col gap-y-8 overflow-y-auto px-6 py-8">
        <div class="flex h-12 shrink-0 items-center">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                @php
                    $logoPath = 'images/logo.png';
                    $logoExists = file_exists(public_path($logoPath));
                @endphp
                @if($logoExists)
                    <img src="{{ asset($logoPath) }}" alt="{{ config('app.name', 'Equidade') }}" class="h-10 w-auto">
                @else
                    <svg class="w-8 h-8 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-2xl font-bold text-indigo-600">{{ config('app.name', 'Equidade') }}</span>
                @endif
            </a>
        </div>

        @php
            $currentRoute = request()->route()?->getName();
            $isActive = function (string $name) use ($currentRoute): bool {
                return $currentRoute ? str_contains($currentRoute, $name) : false;
            };
        @endphp

        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-1">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $currentRoute === 'dashboard' ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                        <span class="flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 p-1.5">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z" />
                            </svg>
                        </span>
                        Painel Clínico
                    </a>
                </li>

                <li class="mt-4">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Atendimento</p>
                </li>

                <li>
                    <a href="{{ route('app.agenda') }}"
                       class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $isActive('agenda') ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Agenda
                    </a>
                </li>

                <li>
                    <a href="{{ route('app.pacientes') }}"
                       class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $isActive('pacientes') ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Pacientes
                    </a>
                </li>

                @if(!Auth::user()->hasRole('Secretaria'))
                    <li>
                        <a href="{{ route('app.evolucoes') }}"
                           class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $isActive('evolucoes') ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Evoluções
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('app.avaliacoes.list') }}"
                       class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $isActive('avaliacoes') && !$isActive('minhas-avaliacoes') ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 002 2m-6 9l2 2 4-4" />
                        </svg>
                        Nova Avaliação
                    </a>
                </li>

                @if(Auth::user()->hasRole('Profissional') || Auth::user()->hasAnyRole(['Admin', 'Coordenador']))
                    <li>
                        <a href="{{ route('app.minhas-avaliacoes') }}"
                           class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $isActive('minhas-avaliacoes') ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Minhas Avaliações
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('app.disponibilidade') }}"
                           class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $isActive('disponibilidade') || $isActive('minha-disponibilidade') ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Disponibilidade
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('app.relatorios') }}"
                       class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $isActive('relatorios') ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Relatórios
                    </a>
                </li>

                @if(Auth::user()->hasRole('Admin'))
                    <li>
                        <a href="{{ route('app.colaboradores') }}"
                           class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ $isActive('colaboradores') ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50/60' }}">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Colaboradores
                        </a>
                    </li>
                @endif
            </ul>

            <div class="mt-auto pt-6">
                <a href="{{ route('app.central-ajuda') }}" 
                   class="block rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-4 shadow-lg transition hover:shadow-xl hover:from-indigo-600 hover:to-purple-700 {{ $isActive('central-ajuda') ? 'ring-2 ring-indigo-300 ring-offset-2' : '' }}">
                    <p class="text-sm font-semibold">Central de Ajuda</p>
                    <p class="mt-2 text-xs text-indigo-50 leading-5">
                        Envie dúvidas, sugestões ou reporte problemas. Estamos aqui para ajudar!
                    </p>
                    <div class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white">
                        Fale conosco
                    </div>
                </a>
            </div>
        </nav>
    </div>
</aside>

