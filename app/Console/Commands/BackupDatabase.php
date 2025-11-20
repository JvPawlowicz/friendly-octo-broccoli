<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--compress : Compress the backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando backup do banco de dados...');

        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port") ?? 3306;

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$database}_{$timestamp}.sql";
        $backupPath = storage_path("app/backups/{$filename}");

        // Criar diretório se não existir
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        // Comando mysqldump
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s -p%s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $this->error('Erro ao criar backup: ' . implode("\n", $output));
            return Command::FAILURE;
        }

        // Comprimir se solicitado
        if ($this->option('compress')) {
            $compressedPath = $backupPath . '.gz';
            exec("gzip -c {$backupPath} > {$compressedPath}", $output, $returnCode);
            
            if ($returnCode === 0) {
                unlink($backupPath);
                $filename .= '.gz';
                $backupPath = $compressedPath;
                $this->info('Backup comprimido com sucesso.');
            }
        }

        $fileSize = filesize($backupPath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);

        $this->info("Backup criado com sucesso!");
        $this->info("Arquivo: {$filename}");
        $this->info("Tamanho: {$fileSizeMB} MB");
        $this->info("Local: {$backupPath}");

        // Limpar backups antigos (manter apenas os últimos 7 dias)
        $this->cleanOldBackups();

        return Command::SUCCESS;
    }

    /**
     * Remove backups mais antigos que 7 dias
     */
    protected function cleanOldBackups(): void
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . '/backup_*.sql*');
        $deleted = 0;

        foreach ($files as $file) {
            if (filemtime($file) < strtotime('-7 days')) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Removidos {$deleted} backup(s) antigo(s).");
        }
    }
}

