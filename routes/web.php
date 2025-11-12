<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\AgendaBoard;
use App\Livewire\AgendaView;
use App\Livewire\AplicarAvaliacao;
use App\Livewire\FormEvolucao;
use App\Livewire\PainelEvolucoes;
use App\Livewire\ProntuarioView;
use App\Livewire\RelatorioFrequencia;
use App\Livewire\RelatorioProdutividade;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard do Profissional (usando PainelEvolucoes)
Route::get('/dashboard', PainelEvolucoes::class)->middleware(['auth', 'verified', 'scope.unit'])->name('dashboard');

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

    // Agenda
    Route::get('/agenda', AgendaView::class)->name('agenda');
    Route::get('/agenda/board', AgendaBoard::class)->name('agenda.board');

    // Pacientes
    Route::get('/pacientes', \App\Livewire\ListaPacientes::class)->name('pacientes');
    Route::get('/pacientes/criar', \App\Livewire\FormPaciente::class)->name('pacientes.create');
    Route::get('/pacientes/{pacienteId}/editar', \App\Livewire\FormPaciente::class)->name('pacientes.edit');
    Route::get('/pacientes/{pacienteId}/prontuario', ProntuarioView::class)->name('pacientes.prontuario');

    // Colaboradores (Apenas Admin)
    Route::get('/colaboradores', \App\Livewire\ListaColaboradores::class)->name('colaboradores');

    // Avaliações
    Route::get('/avaliacoes', AplicarAvaliacao::class)->name('avaliacoes');
    Route::get('/avaliacoes/{avaliacaoId}/editar', AplicarAvaliacao::class)->name('avaliacoes.edit');

    // Relatórios
    Route::view('/relatorios', 'app.relatorios-index')->name('relatorios');
    Route::get('/relatorios/frequencia', RelatorioFrequencia::class)->name('relatorios.frequencia');
    Route::get('/relatorios/produtividade', RelatorioProdutividade::class)->name('relatorios.produtividade');
    
    // Documentos
    Route::get('/documentos/{documento}/download', [\App\Http\Controllers\DocumentoController::class, 'download'])
        ->name('documentos.download');
    Route::get('/documentos/{documento}/visualizar', [\App\Http\Controllers\DocumentoController::class, 'visualizar'])
        ->name('documentos.visualizar');
    
    // Seleção de Unidade
    Route::post('/unidade/selecionar', [\App\Http\Controllers\UnidadeController::class, 'selecionar'])
        ->name('unidade.selecionar');
    
    // Disponibilidade (Profissional)
    Route::get('/minha-disponibilidade', \App\Livewire\MinhaDisponibilidade::class)->name('minha-disponibilidade');
    
    // Minhas Avaliações (Profissional)
    Route::get('/minhas-avaliacoes', \App\Livewire\MinhasAvaliacoes::class)->name('minhas-avaliacoes');
    
    // Meu Perfil (Todas as roles)
    Route::get('/meu-perfil', \App\Livewire\MeuPerfil::class)->name('meu-perfil');
    
    // Gerenciar Responsáveis (Secretaria, Coordenador, Admin)
    Route::get('/pacientes/{pacienteId}/responsaveis', \App\Livewire\GerenciarResponsaveis::class)->name('pacientes.responsaveis');
    
    // Gerenciar Planos de Saúde (Secretaria, Coordenador, Admin)
    Route::get('/planos-saude', \App\Livewire\GerenciarPlanosSaude::class)->name('planos-saude');
    
    // Dashboard Secretaria
    Route::get('/dashboard-secretaria', \App\Livewire\DashboardSecretaria::class)->name('dashboard-secretaria');
    
    // Gerenciar Templates de Avaliação (Coordenador, Admin)
    Route::get('/templates-avaliacao', \App\Livewire\GerenciarTemplatesAvaliacao::class)->name('templates-avaliacao');
    
    // Avaliações da Unidade (Coordenador, Admin)
    Route::get('/avaliacoes-unidade', \App\Livewire\AvaliacoesUnidade::class)->name('avaliacoes-unidade');
    
    // Gerenciar Unidades e Salas (Coordenador, Admin)
    Route::get('/unidades-salas', \App\Livewire\GerenciarUnidadesSalas::class)->name('unidades-salas');
    
    // Dashboard Coordenador
    Route::get('/dashboard-coordenador', \App\Livewire\DashboardCoordenador::class)->name('dashboard-coordenador');
    
    // Dashboard Admin
    Route::get('/dashboard-admin', \App\Livewire\DashboardAdmin::class)->name('dashboard-admin');
});

require __DIR__.'/auth.php';
