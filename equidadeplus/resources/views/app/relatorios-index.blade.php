<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex flex-col gap-2">
                <p class="text-xs uppercase font-semibold text-indigo-500">Relatórios</p>
                <h1 class="text-3xl font-bold text-slate-900">Painel analítico</h1>
                <p class="text-sm text-slate-500 max-w-2xl">Escolha um relatório para acompanhar desempenho clínico e produtividade da equipe. Os filtros salvos ficam disponíveis dentro de cada relatório.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <a href="{{ route('app.relatorios.frequencia') }}"
                   class="group flex flex-col justify-between rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition">
                    <div class="p-6 space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600">
                            Frequência
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900 group-hover:text-indigo-600 transition">Relatório de Frequência</h2>
                            <p class="mt-2 text-sm text-slate-500">Taxa de presença, cancelamentos e visão detalhada por paciente. Ideal para medir engajamento e assiduidade.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 text-sm font-medium text-indigo-600 group-hover:text-indigo-700">
                        Abrir relatório
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>

                <a href="{{ route('app.relatorios.produtividade') }}"
                   class="group flex flex-col justify-between rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition">
                    <div class="p-6 space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-600">
                            Produtividade
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900 group-hover:text-emerald-600 transition">Relatório de Produtividade</h2>
                            <p class="mt-2 text-sm text-slate-500">Acompanhe volume de atendimentos, médias por dia e ocupação das salas para direcionar a agenda.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 text-sm font-medium text-emerald-600 group-hover:text-emerald-700">
                        Abrir relatório
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>

                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-6 flex flex-col justify-between">
                    <div class="space-y-3">
                        <h2 class="text-lg font-semibold text-slate-700">Precisa de outro indicador?</h2>
                        <p class="text-sm text-slate-500">Fale com a equipe de coordenação para priorizarmos novos relatórios (financeiro, satisfação ou metas).</p>
                    </div>
                    <div class="mt-4">
                        <a href="mailto:{{ config('mail.from.address') }}" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                            Sugerir novo relatório
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

