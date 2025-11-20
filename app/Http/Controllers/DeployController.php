<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class DeployController extends Controller
{
    /**
     * Handle deploy webhook request
     */
    public function handle(Request $request)
    {
        // Validar token
        $token = $request->header('X-Deploy-Token') ?? $request->input('token');
        $expectedToken = env('DEPLOY_TOKEN');

        if (empty($expectedToken)) {
            Log::error('Deploy: DEPLOY_TOKEN não configurado no .env');
            return response()->json([
                'success' => false,
                'message' => 'Deploy token não configurado',
            ], 500);
        }

        if ($token !== $expectedToken) {
            Log::warning('Deploy: Tentativa de deploy com token inválido', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Token inválido',
            ], 401);
        }

        // Validar que está em produção
        if (app()->environment() !== 'production') {
            Log::warning('Deploy: Tentativa de deploy em ambiente não-produção', [
                'environment' => app()->environment(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Deploy automático apenas em produção',
            ], 403);
        }

        // Log do início do deploy
        $deployId = Str::uuid();
        Log::info("Deploy iniciado: {$deployId}", [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'branch' => $request->input('ref', 'unknown'),
        ]);

        try {
            // Executar script de deploy em background
            $deployScript = base_path('deploy.sh');
            
            if (!file_exists($deployScript)) {
                throw new \Exception('Script de deploy não encontrado: ' . $deployScript);
            }

            // Executar deploy em background para não timeout
            $process = new Process([
                'bash',
                $deployScript,
            ], base_path(), [
                'PATH' => env('PATH', '/usr/local/bin:/usr/bin:/bin'),
            ]);

            $process->setTimeout(600); // 10 minutos
            $process->start();

            // Salvar PID para monitoramento
            $pid = $process->getPid();
            Log::info("Deploy process iniciado: PID {$pid}", [
                'deploy_id' => $deployId,
            ]);

            // Retornar resposta imediata (deploy roda em background)
            return response()->json([
                'success' => true,
                'message' => 'Deploy iniciado com sucesso',
                'deploy_id' => $deployId,
                'pid' => $pid,
            ], 202);

        } catch (\Exception $e) {
            Log::error("Erro ao iniciar deploy: {$e->getMessage()}", [
                'deploy_id' => $deployId,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar deploy: ' . $e->getMessage(),
                'deploy_id' => $deployId,
            ], 500);
        }
    }

    /**
     * Status do último deploy (opcional, para monitoramento)
     */
    public function status()
    {
        $logFile = storage_path('logs/deploy.log');
        
        if (!file_exists($logFile)) {
            return response()->json([
                'status' => 'unknown',
                'message' => 'Nenhum deploy registrado ainda',
            ]);
        }

        $lastLine = $this->getLastLine($logFile);
        
        return response()->json([
            'status' => 'completed',
            'last_deploy' => $lastLine,
            'log_file' => $logFile,
        ]);
    }

    /**
     * Ler última linha do arquivo de log
     */
    private function getLastLine($file)
    {
        $line = '';
        $f = fopen($file, 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        while ($char === "\n" || $char === "\r") {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        while ($char !== false && $char !== "\n" && $char !== "\r") {
            $line = $char . $line;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        fclose($f);
        return $line;
    }
}

