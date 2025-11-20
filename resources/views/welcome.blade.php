<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Sistema de gestão clínica completo para profissionais de saúde. Gerencie atendimentos, evoluções, avaliações e prontuários de forma eficiente.">

        <title>{{ config('app.name', 'Equidade+') }} - Sistema de Gestão Clínica</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-indigo-50 via-white to-blue-50">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-2xl font-bold text-indigo-600">{{ config('app.name', 'Equidade+') }}</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium transition shadow-sm">
                                Entrar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-20 pb-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                        Gestão Clínica
                        <span class="text-indigo-600">Simplificada</span>
                    </h1>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                        Sistema completo para profissionais de saúde. Gerencie atendimentos, evoluções, avaliações e prontuários de forma eficiente e segura.
                    </p>
                    <div class="flex justify-center">
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-indigo-600 text-white px-8 py-4 rounded-lg hover:bg-indigo-700 font-semibold text-lg transition shadow-lg hover:shadow-xl">
                                Acessar Sistema
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-8 py-4 rounded-lg hover:bg-indigo-700 font-semibold text-lg transition shadow-lg hover:shadow-xl">
                                Entrar no Sistema
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
                <div class="absolute top-40 right-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">
                        Recursos Principais
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Tudo que você precisa para gerenciar sua clínica de forma profissional
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-xl border border-indigo-100 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Agenda Inteligente</h3>
                        <p class="text-gray-600">
                            Calendário visual completo com drag & drop, filtros avançados e visualização por profissional ou sala.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-xl border border-indigo-100 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Prontuário Digital</h3>
                        <p class="text-gray-600">
                            Prontuário completo com timeline de evoluções, avaliações e documentos organizados cronologicamente.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-xl border border-indigo-100 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Evoluções Clínicas</h3>
                        <p class="text-gray-600">
                            Editor rico de texto para evoluções com autosave, rascunhos e workflow de aprovação.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-xl border border-indigo-100 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Relatórios e Análises</h3>
                        <p class="text-gray-600">
                            Relatórios de frequência, produtividade e gráficos interativos para análise de dados.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-xl border border-indigo-100 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Segurança e Privacidade</h3>
                        <p class="text-gray-600">
                            Controle de acesso por perfis, auditoria completa e dados protegidos conforme LGPD.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-xl border border-indigo-100 hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Multi-Unidade</h3>
                        <p class="text-gray-600">
                            Gerencie múltiplas unidades, salas e profissionais em um único sistema integrado.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-gradient-to-r from-indigo-600 to-blue-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl font-bold text-white mb-6">
                    Pronto para começar?
                </h2>
                <p class="text-xl text-indigo-100 mb-8">
                    Acesse o sistema agora e descubra como podemos ajudar na gestão da sua clínica.
                </p>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-lg hover:bg-indigo-50 font-semibold text-lg transition shadow-lg hover:shadow-xl">
                        Acessar Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-lg hover:bg-indigo-50 font-semibold text-lg transition shadow-lg hover:shadow-xl">
                        Entrar no Sistema
                    </a>
                @endauth
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-300 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-white text-lg font-semibold mb-4">{{ config('app.name', 'Equidade+') }}</h3>
                        <p class="text-gray-400">
                            Sistema de gestão clínica completo para profissionais de saúde.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">Recursos</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-white transition">Agenda</a></li>
                            <li><a href="#" class="hover:text-white transition">Prontuário</a></li>
                            <li><a href="#" class="hover:text-white transition">Relatórios</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">Suporte</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-white transition">Documentação</a></li>
                            <li><a href="#" class="hover:text-white transition">Contato</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400 space-y-2">
                    <p>&copy; {{ date('Y') }} Equidade. Todos os direitos reservados.</p>
                    <p>Desenvolvido por <span class="text-white font-medium">João Pawlowicz</span></p>
                </div>
            </div>
        </footer>

        <!-- Animations -->
        <style>
            @keyframes blob {
                0% {
                    transform: translate(0px, 0px) scale(1);
                }
                33% {
                    transform: translate(30px, -50px) scale(1.1);
                }
                66% {
                    transform: translate(-20px, 20px) scale(0.9);
                }
                100% {
                    transform: translate(0px, 0px) scale(1);
                }
            }
            .animate-blob {
                animation: blob 7s infinite;
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-4000 {
                animation-delay: 4s;
            }
        </style>
    </body>
</html>
