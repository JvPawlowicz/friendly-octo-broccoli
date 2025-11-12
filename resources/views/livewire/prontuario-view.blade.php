<div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="xl:col-span-2 bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm uppercase tracking-wide text-indigo-500 font-semibold">Resumo clínico</p>
                                <h2 class="text-2xl font-bold text-slate-900">Paciente 360º</h2>
                                <p class="text-sm text-slate-500">Dados essenciais para decisões rápidas.</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <div class="rounded-xl bg-indigo-50 px-4 py-2 text-center">
                                    <p class="text-xs text-indigo-600">Idade</p>
                                    <p class="text-lg font-semibold text-indigo-700">
                                        {{ $destaques['idade'] ?? '—' }}
                                    </p>
                                </div>
                                <div class="rounded-xl bg-emerald-50 px-4 py-2 text-center">
                                    <p class="text-xs text-emerald-600">Plano</p>
                                    <p class="text-sm font-semibold text-emerald-700">
                                        {{ $destaques['plano'] ?? '—' }}
                                    </p>
                                </div>
                                <div class="rounded-xl bg-amber-50 px-4 py-2 text-center">
                                    <p class="text-xs text-amber-600">Pendências</p>
                                    <p class="text-sm font-semibold text-amber-700">
                                        {{ ($destaques['pendencias']['evolucoes'] ?? 0) + ($destaques['pendencias']['avaliacoes'] ?? 0) }} itens
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-600">Responsáveis principais</h3>
                                    <ul class="mt-2 space-y-2 text-sm text-slate-600">
                                        @forelse($destaques['responsaveis'] ?? [] as $responsavel)
                                            <li class="flex items-start gap-2">
                                                <span class="mt-1 inline-flex h-2 w-2 rounded-full bg-indigo-400"></span>
                                                <div>
                                                    <p class="font-medium text-slate-800">{{ $responsavel['nome'] }}</p>
                                                    <p class="text-xs text-slate-500">{{ $responsavel['parentesco'] ?? 'Responsável' }}</p>
                                                    @if(!empty($responsavel['telefone']))
                                                        <p class="text-xs text-slate-400">{{ $responsavel['telefone'] }}</p>
                                                    @endif
                                                </div>
                                            </li>
                                        @empty
                                            <li class="text-xs text-slate-400">Nenhum responsável cadastrado.</li>
                                        @endforelse
                                    </ul>
                                </div>

                                @if(!empty($destaques['diagnostico']) || !empty($destaques['alergias']))
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                                        @if(!empty($destaques['diagnostico']))
                                            <p class="text-xs uppercase font-semibold text-slate-500">Diagnóstico / Condição</p>
                                            <p class="text-sm text-slate-700 mt-1">{{ $destaques['diagnostico'] }}</p>
                                        @endif
                                        @if(!empty($destaques['alergias']))
                                            <p class="text-xs uppercase font-semibold text-rose-500 mt-3">Alergias / Medicações</p>
                                            <p class="text-sm text-slate-700 mt-1">{{ $destaques['alergias'] }}</p>
                                        @endif
                                        @if(!empty($destaques['plano_crise']))
                                            <p class="text-xs uppercase font-semibold text-amber-500 mt-3">Plano de crise</p>
                                            <p class="text-sm text-slate-700 mt-1">{{ $destaques['plano_crise'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-5">
                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4">
                                    <p class="text-xs uppercase text-indigo-500 font-semibold">Próximo atendimento</p>
                                    @if($destaques['proximo_atendimento'])
                                        <p class="mt-2 text-lg font-semibold text-indigo-800">{{ $destaques['proximo_atendimento']['horario'] }}</p>
                                        <p class="text-sm text-indigo-600">Com {{ $destaques['proximo_atendimento']['profissional'] ?? 'Profissional' }}</p>
                                        <p class="text-xs text-indigo-400 mt-1">Sala: {{ $destaques['proximo_atendimento']['sala'] ?? '—' }} | Status {{ $destaques['proximo_atendimento']['status'] }}</p>
                                    @else
                                        <p class="mt-2 text-sm text-indigo-500">Nenhum atendimento futuro agendado.</p>
                                    @endif
                                </div>

                                <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-4">
                                    <p class="text-xs uppercase text-emerald-600 font-semibold">Última evolução finalizada</p>
                                    @if($destaques['ultima_evolucao'])
                                        <p class="mt-2 text-lg font-semibold text-emerald-700">{{ $destaques['ultima_evolucao']['data'] }}</p>
                                        <p class="text-sm text-emerald-600">Profissional {{ $destaques['ultima_evolucao']['profissional'] ?? '—' }}</p>
                                    @else
                                        <p class="mt-2 text-sm text-emerald-600">Nenhuma evolução finalizada registrada.</p>
                                    @endif
                                </div>

                                <div class="rounded-xl border border-amber-100 bg-white p-4">
                                    <p class="text-xs uppercase text-amber-500 font-semibold">Pendências abertas</p>
                                    <div class="mt-3 space-y-2 text-sm text-slate-600">
                                        <div class="flex items-center justify-between">
                                            <span>Evoluções</span>
                                            <span class="font-semibold text-amber-600">{{ $destaques['pendencias']['evolucoes'] ?? 0 }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Avaliações</span>
                                            <span class="font-semibold text-amber-600">{{ $destaques['pendencias']['avaliacoes'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 flex flex-col gap-4">
                        <div>
                            <p class="text-xs uppercase font-semibold text-slate-500">Ações rápidas</p>
                            <p class="text-sm text-slate-500">Ganhe tempo executando as tarefas mais comuns.</p>
                        </div>
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('app.agenda') }}"
                               class="inline-flex items-center justify-between rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                                <span>Agendar atendimento</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h12m-6 14l6-7-6-7" />
                                </svg>
                            </a>

                            <a href="{{ route('app.evolucoes.create', ['paciente_id' => $pacienteId]) }}"
                               class="inline-flex items-center justify-between rounded-xl border border-purple-100 bg-purple-50 px-4 py-3 text-sm font-semibold text-purple-700 hover:bg-purple-100">
                                <span>Registrar evolução clínica</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </a>

                            <button type="button" wire:click="abrirModalUpload"
                                    class="inline-flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                                <span>Anexar documento</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <p class="text-xs uppercase font-semibold text-slate-500">Documentos recentes</p>
                            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                                @forelse($documentosRecentes as $doc)
                                    <li class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $doc['titulo'] }}</p>
                                            <p class="text-xs text-slate-400">
                                                {{ optional($doc['created_at'])->format('d/m/Y H:i') }} • {{ $doc['usuario'] ?? 'Equipe' }}
                                            </p>
                                        </div>
                                        <a href="{{ route('app.pacientes.documentos.download', [$pacienteId, $doc['id']]) }}" class="text-xs text-indigo-600 hover:text-indigo-800">
                                            Baixar
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-xs text-slate-400">Nenhum documento recente.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
            </div>

                <!-- Informações do Paciente -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Foto e Dados Principais -->
                        <div class="md:col-span-1">
                            <div class="flex flex-col items-center">
                            @if($paciente->foto_perfil)
                                <img src="{{ asset('storage/' . $paciente->foto_perfil) }}" 
                                     alt="{{ $paciente->nome_completo }}" 
                                         class="h-32 w-32 rounded-full object-cover mb-4">
                                @else
                                    <div class="h-32 w-32 rounded-full bg-gray-300 flex items-center justify-center mb-4">
                                        <span class="text-gray-600 text-3xl font-medium">
                                            {{ strtoupper(substr($paciente->nome_completo, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <h3 class="text-2xl font-bold text-center">{{ $paciente->nome_completo }}</h3>
                                @if($paciente->nome_social)
                                    <p class="text-gray-600 text-center">({{ $paciente->nome_social }})</p>
                                @endif
                                <span class="mt-2 px-3 py-1 rounded-full text-sm font-medium
                                    {{ $paciente->status === 'Ativo' ? 'bg-green-100 text-green-800' : 
                                       ($paciente->status === 'Inativo' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $paciente->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Dados de Identificação -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-semibold mb-4">Dados de Identificação</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Data de Nascimento</p>
                                    <p class="font-medium">{{ $paciente->data_nascimento ? $paciente->data_nascimento->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">CPF</p>
                                    <p class="font-medium">{{ $paciente->cpf ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium">{{ $paciente->email_principal ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Telefone</p>
                                    <p class="font-medium">{{ $paciente->telefone_principal ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    @if($paciente->logradouro || $paciente->cep)
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="text-lg font-semibold mb-4">Endereço</h4>
                        <p class="text-gray-700">
                            {{ $paciente->logradouro ?? '' }}
                            {{ $paciente->numero ? ', ' . $paciente->numero : '' }}
                            {{ $paciente->complemento ? ' - ' . $paciente->complemento : '' }}<br>
                            {{ $paciente->bairro ?? '' }}
                            {{ $paciente->cidade ? ' - ' . $paciente->cidade : '' }}
                            {{ $paciente->estado ? '/' . $paciente->estado : '' }}<br>
                            {{ $paciente->cep ? 'CEP: ' . $paciente->cep : '' }}
                        </p>
                    </div>
                    @endif

                    <!-- Plano de Saúde -->
                    @if($paciente->planoSaude)
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="text-lg font-semibold mb-4">Plano de Saúde</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Plano</p>
                                <p class="font-medium">{{ $paciente->planoSaude->nome_plano }}</p>
                            </div>
                            @if($paciente->numero_carteirinha)
                            <div>
                                <p class="text-sm text-gray-500">Nº Carteirinha</p>
                                <p class="font-medium">{{ $paciente->numero_carteirinha }}</p>
                            </div>
                            @endif
                            @if($paciente->validade_carteirinha)
                            <div>
                                <p class="text-sm text-gray-500">Validade</p>
                                <p class="font-medium">{{ $paciente->validade_carteirinha->format('d/m/Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Unidade -->
                    @if($paciente->unidadePadrao)
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="text-lg font-semibold mb-4">Unidade</h4>
                        <p class="font-medium">{{ $paciente->unidadePadrao->nome }}</p>
                    </div>
                    @endif

                    <!-- Dados Clínicos -->
                    @if($paciente->diagnostico_condicao || $paciente->plano_de_crise || $paciente->alergias_medicacoes)
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="text-lg font-semibold mb-4">Dados Clínicos</h4>
                        @if($paciente->diagnostico_condicao)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Diagnóstico / Condição</p>
                            <p class="text-gray-700">{{ $paciente->diagnostico_condicao }}</p>
                        </div>
                        @endif
                        @if($paciente->plano_de_crise)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Plano de Crise</p>
                            <p class="text-gray-700">{{ $paciente->plano_de_crise }}</p>
                        </div>
                        @endif
                        @if($paciente->alergias_medicacoes)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Alergias e Medicações</p>
                            <p class="text-gray-700">{{ $paciente->alergias_medicacoes }}</p>
                        </div>
                        @endif
                        @if($paciente->metodo_comunicacao)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Método de Comunicação</p>
                            <p class="text-gray-700">{{ $paciente->metodo_comunicacao }}</p>
                        </div>
                        @endif
                        @if($paciente->informacoes_escola)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Informações da Escola</p>
                            <p class="text-gray-700">{{ $paciente->informacoes_escola }}</p>
                        </div>
                        @endif
                        @if($paciente->informacoes_medicas_adicionais)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Outras Informações Médicas</p>
                            <p class="text-gray-700">{{ $paciente->informacoes_medicas_adicionais }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Responsáveis -->
                    <div class="mt-6 pt-6 border-t">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold">Responsáveis</h4>
                            @if(Auth::user()->can('editar_paciente') || Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria']))
                            <a href="{{ route('app.pacientes.responsaveis', $pacienteId) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded hover:bg-indigo-200 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Gerenciar Responsáveis
                            </a>
                            @endif
                        </div>
                        @if($paciente->responsaveis && $paciente->responsaveis->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($paciente->responsaveis as $responsavel)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="font-medium">{{ $responsavel->nome_completo }}</p>
                                    <div class="flex items-center space-x-1">
                                        @if($responsavel->is_responsavel_legal)
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Legal</span>
                                        @endif
                                        @if($responsavel->is_contato_emergencia)
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Emergência</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">{{ $responsavel->parentesco ?? 'Responsável' }}</p>
                                @if($responsavel->telefone_principal)
                                <p class="text-sm text-gray-600 mt-1">Tel: {{ $responsavel->telefone_principal }}</p>
                                @endif
                                @if($responsavel->email)
                                <p class="text-sm text-gray-600">Email: {{ $responsavel->email }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-500">Nenhum responsável cadastrado</p>
                        @endif
                    </div>

                    <!-- Atendimentos Recentes -->
                    @if($paciente->atendimentos && $paciente->atendimentos->count() > 0)
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="text-lg font-semibold mb-4">Atendimentos Recentes</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data/Hora</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Profissional</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sala</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($paciente->atendimentos->sortByDesc('data_hora_inicio')->take(10) as $atendimento)
                                    <tr>
                                        <td class="px-4 py-2 text-sm">
                                            {{ $atendimento->data_hora_inicio->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">{{ $atendimento->profissional->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $atendimento->sala->nome ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $atendimento->status === 'Concluído' ? 'bg-green-100 text-green-800' : 
                                                   ($atendimento->status === 'Cancelado' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ $atendimento->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    </div>
                </div>

                <!-- Linha do Tempo -->
                <div id="documentos" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-lg font-semibold">Linha do Tempo Clínica</h2>
                        <div class="flex items-center gap-2">
                            <button wire:click="exportarLinhaTempo"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 shadow-sm transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m0 0l-3-3m3 3l3-3m2 6H7a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2" />
                                </svg>
                                Exportar CSV
                            </button>
                            @if(Auth::user()->can('upload_documento') || Auth::user()->hasAnyRole(['Admin', 'Coordenador', 'Secretaria']))
                            <button wire:click="abrirModalUpload" 
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Adicionar Documento
                            </button>
                            @endif
                        </div>
                    </div>
                        
                        @if(empty($linhaTempo))
                        <p class="text-gray-500 text-center py-8">Nenhum registro clínico finalizado ainda.</p>
                        @else
                        <div class="space-y-8">
                                @foreach($linhaTempo as $item)
                                
                                    @if($item['tipo'] === 'evolucao')
                                    @php $evolucao = $item['dados']; @endphp
                                    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-bold text-lg text-blue-700">Evolução Clínica</span>
                                            <span class="text-sm text-gray-500">{{ ($evolucao->finalizado_em ?? $evolucao->created_at)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            Profissional: {{ $evolucao->profissional->name }}
                                        </p>
                                        
                                        <div class="prose prose-sm max-w-none">
                                            <p><strong>Relato:</strong> {{ $evolucao->relato_clinico }}</p>
                                            @if($evolucao->conduta) 
                                                <p><strong>Conduta:</strong> {{ $evolucao->conduta }}</p> 
                                            @endif
                                            @if($evolucao->objetivos) 
                                                <p><strong>Objetivos:</strong> {{ $evolucao->objetivos }}</p> 
                                            @endif
                                        </div>

                                        <div class="mt-3 text-right">
                                            <button wire:click="abrirModalAdendo({{ $evolucao->id }})"
                                                    class="text-xs bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300 transition">
                                                + Adicionar Adendo
                                            </button>
                                        </div>

                                        @if($evolucao->adendos && $evolucao->adendos->isNotEmpty())
                                            <div class="mt-4 pl-4 border-l-2 border-gray-200 space-y-3">
                                                @foreach($evolucao->adendos as $adendo)
                                                    <div class="bg-gray-50 p-3 rounded-md">
                                                        <p class="text-xs text-gray-500 mb-1">
                                                            <strong>Adendo</strong> por {{ $adendo->profissional->name }} 
                                                            em {{ $adendo->created_at->format('d/m/Y H:i') }}
                                                        </p>
                                                        <div class="prose prose-sm max-w-none text-gray-700">
                                                            <p>{{ $adendo->relato_clinico }}</p>
                                                            @if($adendo->conduta) 
                                                                <p><strong>Conduta:</strong> {{ $adendo->conduta }}</p> 
                                                            @endif
                                                            @if($adendo->objetivos) 
                                                                <p><strong>Objetivos:</strong> {{ $adendo->objetivos }}</p> 
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($item['tipo'] === 'avaliacao')
                                    @php $avaliacao = $item['dados']; @endphp
                                    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-bold text-lg text-green-700">Avaliação: {{ $avaliacao->template->nome_template }}</span>
                                            <span class="text-sm text-gray-500">{{ $avaliacao->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            Profissional: {{ $avaliacao->profissional->name }}
                                        </p>
                                    </div>
                                @endif

                                @if($item['tipo'] === 'documento')
                                    @php $documento = $item['dados']; @endphp
                                    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-purple-500">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1">
                                                <span class="font-bold text-lg text-purple-700">{{ $documento->titulo_documento }}</span>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    Categoria: {{ $documento->categoria ?? 'Sem categoria' }} | Upload por: {{ $documento->user->name ?? 'N/A' }}
                                                </p>
                                                <p class="text-sm text-gray-500 mt-1">{{ $documento->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2 ml-4">
                                                @php
                                                    $isPdf = str_ends_with(strtolower($documento->path_arquivo), '.pdf');
                                                    $isImage = in_array(strtolower(pathinfo($documento->path_arquivo, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                                                @endphp
                                                @if($isPdf || $isImage)
                                                <a href="{{ route('app.documentos.visualizar', $documento->id) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Ver
                                                </a>
                                                @endif
                                                <a href="{{ route('app.documentos.download', $documento->id) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-xs font-medium rounded hover:bg-green-200 transition">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                    Baixar
                                                </a>
                                                @if(Auth::user()->can('apagar_documento') || Auth::user()->hasAnyRole(['Admin', 'Coordenador']))
                                                <button wire:click="confirmarDeletarDocumento({{ $documento->id }})"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded hover:bg-red-200 transition">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Deletar
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <!-- Modal de Upload de Documento -->
    @if($mostrarModalUpload)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="fecharModalUpload">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Adicionar Documento</h3>
                    <button wire:click="fecharModalUpload" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="salvarDocumento">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título do Documento *</label>
                        <input type="text" wire:model="titulo_documento" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('titulo_documento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select wire:model="categoria" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Selecione uma categoria</option>
                            <option value="Laudo">Laudo</option>
                            <option value="Exame">Exame</option>
                            <option value="Receita">Receita</option>
                            <option value="Atestado">Atestado</option>
                            <option value="Outro">Outro</option>
                        </select>
                        @error('categoria') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Arquivo *</label>
                        <input type="file" wire:model="arquivo" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('arquivo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @if($arquivo)
                            <p class="text-xs text-gray-500 mt-1">Arquivo selecionado: {{ $arquivo->getClientOriginalName() }}</p>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="fecharModalUpload" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:from-indigo-700 hover:to-purple-700 transition">
                            Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de Confirmação de Exclusão -->
    @if($mostrarModalConfirmacao && $documentoParaDeletar)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="fecharModalConfirmacao">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Confirmar Exclusão</h3>
                <p class="text-sm text-gray-500 text-center mb-6">
                    Tem certeza que deseja deletar o documento <strong>"{{ $documentoParaDeletar->titulo_documento }}"</strong>? Esta ação não pode ser desfeita.
                </p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="fecharModalConfirmacao" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button wire:click="deletarDocumento" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Deletar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
