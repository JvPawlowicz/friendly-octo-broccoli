<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Colaboradores</h2>
                            <p class="mt-1 text-sm text-gray-500">Gerencie todos os usuários e permissões do sistema</p>
                        </div>
                        <button wire:click="abrirModal" 
                                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Novo Colaborador
                        </button>
                    </div>

                    <!-- Filtros -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <input type="text" 
                                   wire:model.live.debounce.300ms="search" 
                                   placeholder="Nome, Email ou Cargo..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Perfil</label>
                            <select wire:model.live="roleFilter" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model.live="statusFilter" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unidade</label>
                            <select wire:model.live="unidadeFilter" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas</option>
                                @foreach($unidades as $unidade)
                                    <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Lista de Colaboradores -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($colaboradores as $colaborador)
                        <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg hover:border-indigo-300 transition-all duration-200 transform hover:-translate-y-1">
                            <div class="flex items-center space-x-4">
                                <!-- Foto -->
                                <div class="flex-shrink-0">
                                    @if($colaborador->foto_perfil)
                                        <img src="{{ asset('storage/' . $colaborador->foto_perfil) }}" 
                                             alt="{{ $colaborador->name }}" 
                                             class="h-16 w-16 rounded-full object-cover">
                                    @else
                                        <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-600 text-xl font-medium">
                                                {{ strtoupper(substr($colaborador->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Informações -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                                        {{ $colaborador->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ $colaborador->email }}
                                    </p>
                                    @if($colaborador->cargo)
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $colaborador->cargo }}
                                    </p>
                                    @endif
                                    
                                    <!-- Perfis (Roles) -->
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($colaborador->roles as $role)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $role->name === 'Admin' ? 'bg-purple-100 text-purple-800' : 
                                               ($role->name === 'Coordenador' ? 'bg-blue-100 text-blue-800' : 
                                               ($role->name === 'Profissional' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ $role->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Status -->
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $colaborador->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $colaborador->status ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Unidades -->
                                    @if($colaborador->unidades->count() > 0)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500">Unidades:</p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($colaborador->unidades as $unidade)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                                                {{ $unidade->nome }}
                                            </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Ações -->
                            <div class="mt-4 flex space-x-2">
                                <button wire:click="abrirModal({{ $colaborador->id }})"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>
                                <button wire:click="toggleStatus({{ $colaborador->id }})"
                                        class="px-3 py-2 text-sm font-medium rounded-lg transition-colors
                                        {{ $colaborador->status ? 'text-yellow-700 bg-yellow-50 hover:bg-yellow-100' : 'text-green-700 bg-green-50 hover:bg-green-100' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($colaborador->status)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @endif
                                    </svg>
                                </button>
                                @if($colaborador->id !== Auth::id())
                                <button wire:click="deletar({{ $colaborador->id }})"
                                        onclick="return confirm('Tem certeza que deseja remover este colaborador?')"
                                        class="px-3 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500">Nenhum colaborador encontrado.</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $colaboradores->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Criar/Editar Colaborador -->
    @if($mostrarModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="fecharModal">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white" wire:click.stop>
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $colaboradorId ? 'Editar' : 'Criar' }} Colaborador
                    </h3>
                    <button wire:click="fecharModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="salvar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto de Perfil</label>
                            <input type="file" wire:model="foto_perfil_nova" 
                                   accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('foto_perfil_nova') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                            <input type="text" wire:model="name" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" wire:model="email" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Senha {{ $colaboradorId ? '(deixe em branco para manter)' : '*' }}
                            </label>
                            <input type="password" wire:model="password" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
                            <input type="password" wire:model="password_confirmation" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                            <input type="text" wire:model="cargo" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Conselho Profissional</label>
                            <input type="text" wire:model="conselho_profissional" 
                                   placeholder="Ex: CRP, CFP..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número do Conselho</label>
                            <input type="text" wire:model="numero_conselho" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Especialidades</label>
                            <textarea wire:model="especialidades" rows="3" 
                                      placeholder="Liste as especialidades separadas por vírgula..."
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Perfis (Roles) *</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-md p-3">
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="roles_selecionados" 
                                               value="{{ $role->id }}"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label class="ml-2 text-sm text-gray-700">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles_selecionados') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unidades</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-md p-3">
                                @foreach($unidades as $unidade)
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="unidades_selecionadas" 
                                               value="{{ $unidade->id }}"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label class="ml-2 text-sm text-gray-700">{{ $unidade->nome }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="status" 
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label class="ml-2 text-sm text-gray-700">Usuário Ativo</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="fecharModal" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:from-indigo-700 hover:to-purple-700 transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

