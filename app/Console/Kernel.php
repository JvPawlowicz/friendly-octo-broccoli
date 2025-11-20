<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Backup diário do banco de dados às 2h da manhã
        $schedule->command('backup:database --compress')
            ->dailyAt('02:00')
            ->timezone('America/Sao_Paulo')
            ->onFailure(function () {
                // Notificar admin em caso de falha (implementar notificação)
                \Log::error('Falha no backup automático do banco de dados');
            });

        // Limpar cache de sessões antigas (semanal)
        $schedule->command('session:gc')
            ->weekly()
            ->sundays()
            ->at('03:00');

        // Limpar logs antigos (mensal)
        $schedule->call(function () {
            // Limpar logs com mais de 30 dias
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');
            $cutoff = now()->subDays(30)->timestamp;
            
            foreach ($files as $file) {
                if (file_exists($file) && filemtime($file) < $cutoff) {
                    unlink($file);
                }
            }
        })->monthly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

