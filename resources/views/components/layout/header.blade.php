@php
    $user = Auth::user();
    $unidadeSelecionada = session('unidade_selecionada');
    $unidadesDisponiveis = $user->hasRole('Admin')
        ? \App\Models\Unidade::orderBy('nome')->get()
        : $user->unidades()->orderBy('nome')->get();
    $cards = collect($dashboardStats['cards'] ?? []);
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/80 backdrop-blur">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <div class="flex flex-1 items-center gap-4">
                <button type="button" class="lg:hidden inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm">
                    <span class="sr-only">Abrir menu</span>
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="hidden md:flex md:flex-1">
                    @livewire('busca-global')
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($unidadesDisponiveis->count() > 1)
                    <form method="POST" action="{{ route('app.unidade.selecionar') }}" class="hidden sm:flex items-center gap-2 text-sm text-slate-600">
                        @csrf
                        <label class="sr-only">Selecionar unidade</label>
                        <select name="unidade_id" onchange="this.form.submit()" class="rounded-lg border-slate-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-w-[180px]">
                            <option value="">Todas as unidades</option>
                            @foreach($unidadesDisponiveis as $unidade)
                                <option value="{{ $unidade->id }}" {{ (string) $unidadeSelecionada === (string) $unidade->id ? 'selected' : '' }}>
                                    {{ $unidade->nome }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex sm:flex-col sm:items-end">
                        <span class="text-sm font-semibold text-slate-700">{{ $user->name }}</span>
                        <span class="text-xs text-slate-400">{{ $user->roles->pluck('name')->join(', ') }}</span>
                    </div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center justify-center gap-2 rounded-full border border-transparent bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-600 shadow-sm hover:bg-indigo-100 focus:outline-none">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 10-6 0 3 3 0 006 0z" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-3 py-2 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-700">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('app.meu-perfil')">Meu Perfil</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">Configurações</x-dropdown-link>
                            @if($user->hasAnyRole(['Admin', 'Coordenador']))
                                <x-dropdown-link :href="route('filament.admin.pages.dashboard')">Admin Panel</x-dropdown-link>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>

    @if($cards->isNotEmpty())
    <div class="px-4 sm:px-6 lg:px-8 pb-4">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($cards as $card)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-800">{{ $card['value'] }}</p>
                    @if(!empty($card['description']))
                        <p class="text-xs text-slate-400">{{ $card['description'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</header>

