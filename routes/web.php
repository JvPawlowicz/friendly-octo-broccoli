<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\AplicarAvaliacao;
use App\Livewire\FormEvolucao;
use App\Livewire\PainelEvolucoes;
use App\Livewire\ProntuarioView;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard Unificado (adaptativo por role)
Route::get('/dashboard', \App\Livewire\Dashboard::class)->middleware(['auth', 'verified', 'scope.unit'])->name('dashboard');

Route::middleware(['auth', 'scope.unit'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================
// ROTAS DO APP (Fora do Filament)
// ============================================
Route::middleware(['auth', 'scope.unit'])->prefix('app')->name('app.')->group(function () {
    // Dashboard / Evoluções Pendentes (Secretaria não tem acesso - verificação no componente)
    Route::get('/evolucoes', PainelEvolucoes::class)->name('evolucoes');
    Route::get('/evolucoes/criar', FormEvolucao::class)->name('evolucoes.create');
    Route::get('/evolucoes/{evolucaoId}/editar', FormEvolucao::class)->name('evolucoes.edit');

    // Agenda (consolidada - calendário e board)
    Route::get('/agenda', \App\Livewire\Agenda::class)->name('agenda');

    // Pacientes
    Route::get('/pacientes', \App\Livewire\ListaPacientes::class)->name('pacientes');
    Route::get('/pacientes/criar', \App\Livewire\FormPaciente::class)->name('pacientes.create');
    Route::get('/pacientes/{pacienteId}/editar', \App\Livewire\FormPaciente::class)->name('pacientes.edit');
    Route::get('/pacientes/{pacienteId}/prontuario', ProntuarioView::class)->name('pacientes.prontuario');

    // Colaboradores (Apenas Admin)
    Route::get('/colaboradores', \App\Livewire\ListaColaboradores::class)->name('colaboradores');

    // Avaliações - Criar/Editar
    Route::get('/avaliacoes/criar', AplicarAvaliacao::class)->name('avaliacoes.create');
    Route::get('/avaliacoes/{avaliacaoId}/editar', AplicarAvaliacao::class)->name('avaliacoes.edit');
    
    // Lista de Avaliações (consolidado - adaptativo por role)
    Route::get('/avaliacoes', \App\Livewire\AvaliacoesList::class)->name('avaliacoes.list');
    Route::get('/minhas-avaliacoes', \App\Livewire\AvaliacoesList::class)->name('minhas-avaliacoes'); // Alias para compatibilidade
    Route::get('/avaliacoes-unidade', \App\Livewire\AvaliacoesList::class)->name('avaliacoes-unidade'); // Alias para compatibilidade

    // Relatórios (consolidado - com abas)
    Route::get('/relatorios', \App\Livewire\Relatorios::class)->name('relatorios');
    Route::get('/relatorios/frequencia', \App\Livewire\Relatorios::class)->name('relatorios.frequencia'); // Alias
    Route::get('/relatorios/produtividade', \App\Livewire\Relatorios::class)->name('relatorios.produtividade'); // Alias
    
    // Documentos
    Route::get('/documentos/{documento}/download', [\App\Http\Controllers\DocumentoController::class, 'download'])
        ->name('documentos.download');
    Route::get('/documentos/{documento}/visualizar', [\App\Http\Controllers\DocumentoController::class, 'visualizar'])
        ->name('documentos.visualizar');
    
    // Seleção de Unidade
    Route::post('/unidade/selecionar', [\App\Http\Controllers\UnidadeController::class, 'selecionar'])
        ->name('unidade.selecionar');
    
    // Disponibilidade (Profissional, Coordenador, Admin)
    Route::get('/disponibilidade', \App\Livewire\MinhaDisponibilidade::class)->name('disponibilidade');
    Route::get('/minha-disponibilidade', \App\Livewire\MinhaDisponibilidade::class)->name('minha-disponibilidade'); // Alias para compatibilidade
    
    // Meu Perfil (Todas as roles)
    Route::get('/meu-perfil', \App\Livewire\MeuPerfil::class)->name('meu-perfil');
    
    // Gerenciar Responsáveis (Secretaria, Coordenador, Admin)
    Route::get('/pacientes/{pacienteId}/responsaveis', \App\Livewire\GerenciarResponsaveis::class)->name('pacientes.responsaveis');
    
    // Gerenciar Planos de Saúde (Secretaria, Coordenador, Admin)
    Route::get('/planos-saude', \App\Livewire\GerenciarPlanosSaude::class)->name('planos-saude');
    
    // Gerenciar Templates de Avaliação (Coordenador, Admin)
    Route::get('/templates-avaliacao', \App\Livewire\GerenciarTemplatesAvaliacao::class)->name('templates-avaliacao');
    
    // Gerenciar Unidades e Salas (Coordenador, Admin)
    Route::get('/unidades-salas', \App\Livewire\GerenciarUnidadesSalas::class)->name('unidades-salas');
    
    // Central de Ajuda (Todas as roles)
    Route::get('/central-ajuda', \App\Livewire\CentralAjuda::class)->name('central-ajuda');
});

// Health check para monitoramento
Route::get('/health', function () {
    try {
        $dbStatus = \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'connected' : 'disconnected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected';
    }
    
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'environment' => app()->environment(),
        'database' => $dbStatus,
        'version' => app()->version(),
    ]);
})->name('health');

require __DIR__.'/auth.php';
