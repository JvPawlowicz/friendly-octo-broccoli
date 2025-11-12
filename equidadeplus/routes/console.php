<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// COMANDOS AGENDADOS (Módulo 11 - Automação)
// ============================================

// Verificar Evoluções Pendentes (diariamente)
Schedule::command('evolucoes:verificar-pendentes')
    ->daily()
    ->at('08:00')
    ->description('Verifica evoluções pendentes há mais de 48 horas');

// Verificar Inatividade LGPD (mensalmente)
Schedule::command('lgpd:verificar-inatividade')
    ->monthly()
    ->at('09:00')
    ->description('Verifica pacientes inativos há mais de 2 anos para revisão LGPD');
