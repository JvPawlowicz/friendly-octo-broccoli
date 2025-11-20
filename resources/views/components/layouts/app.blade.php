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
            window.AppToast = function(message, type = 'info', duration = 4000) {
                const container = document.getElementById('app-toast-container');
                if (!container) {
                    // Criar container se não existir
                    const newContainer = document.createElement('div');
                    newContainer.id = 'app-toast-container';
                    newContainer.className = 'fixed top-20 right-6 z-50 space-y-2 pointer-events-none';
                    document.body.appendChild(newContainer);
                    return window.AppToast(message, type, duration);
                }

                const configs = {
                    success: {
                        border: '#15803d',
                        bg: '#dcfce7',
                        text: '#14532d',
                        icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                    },
                    error: {
                        border: '#dc2626',
                        bg: '#fee2e2',
                        text: '#7f1d1d',
                        icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                    },
                    warning: {
                        border: '#d97706',
                        bg: '#fef3c7',
                        text: '#78350f',
                        icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>'
                    },
                    info: {
                        border: '#4338ca',
                        bg: '#eef2ff',
                        text: '#312e81',
                        icon: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                    }
                };

                const config = configs[type] || configs.info;
                const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                
                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = 'pointer-events-auto bg-white border-l-4 shadow-xl rounded-lg p-4 w-80 animate-slide-in-right';
                toast.style.borderColor = config.border;
                toast.style.backgroundColor = config.bg;
                toast.style.color = config.text;

                toast.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0" style="color: ${config.border}">
                            ${config.icon}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium leading-relaxed">${message}</p>
                        </div>
                        <button type="button" onclick="document.getElementById('${toastId}').remove()" 
                                class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                `;

                container.appendChild(toast);

                // Auto-remover após duração
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    toast.style.transition = 'all 0.3s ease-out';
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            };
        }

        window.addEventListener('app:toast', (event) => {
            const detail = event.detail || {};
            window.AppToast(
                detail.message || 'Operação realizada com sucesso.',
                detail.type || 'info',
                detail.duration || 4000
            );
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

