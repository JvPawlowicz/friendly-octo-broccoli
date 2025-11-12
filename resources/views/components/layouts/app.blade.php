<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Equidade+') }} - App</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-50">
    <a href="#conteudo-principal" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-4 focus:left-4 focus:px-4 focus:py-2 focus:bg-white focus:text-indigo-600 focus:rounded-lg focus:shadow">Ir para o conteúdo principal</a>

    <div class="min-h-screen lg:flex bg-slate-50">
        <x-layout.sidebar />

        <div class="flex-1 lg:pl-72">
            <x-layout.header :dashboard-stats="$dashboardStats ?? []" />

            <div class="px-4 sm:px-6 lg:px-8">
                @include('components.ui.flash-messages')
            </div>
            <div id="app-toast-container" class="fixed top-6 right-6 z-40 space-y-2 pointer-events-none"></div>

            <main id="conteudo-principal" class="px-4 sm:px-6 lg:px-8 pb-16">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewire('wire-elements-modal')
    @livewireScripts
    @stack('scripts')

    <script>
        if (!window.AppToast) {
            window.AppToast = function(message, type = 'info') {
                const container = document.getElementById('app-toast-container');
                if (!container) return;

                const colors = {
                    info: { border: '#4338ca', bg: '#eef2ff', text: '#312e81' },
                    success: { border: '#15803d', bg: '#dcfce7', text: '#14532d' },
                    error: { border: '#dc2626', bg: '#fee2e2', text: '#7f1d1d' },
                };

                const palette = colors[type] || colors.info;
                const toast = document.createElement('div');
                toast.className = 'pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-lg border-l-4 transition translate-x-0 opacity-100';
                toast.style.borderColor = palette.border;
                toast.style.backgroundColor = palette.bg;
                toast.style.color = palette.text;

                toast.innerHTML = `<span class="text-sm font-medium">${message}</span>`;
                container.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(10px)';
                    setTimeout(() => toast.remove(), 250);
                }, 3500);
            };
        }

        window.addEventListener('app:toast', (event) => {
            const detail = event.detail || {};
            window.AppToast(detail.message || 'Operação realizada com sucesso.', detail.type || 'info');
        });
    </script>

    <style>
        @keyframes slide-in-right {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in-right { animation: slide-in-right 0.3s ease-out both; }
    </style>
</body>
</html>

