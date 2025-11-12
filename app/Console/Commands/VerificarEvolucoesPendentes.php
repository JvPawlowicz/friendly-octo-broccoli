<?php

namespace App\Console\Commands;

use App\Models\Evolucao;
use Illuminate\Console\Command;

class VerificarEvolucoesPendentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evolucoes:verificar-pendentes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica evoluções pendentes há mais de 48 horas (Item 11)';

    /**
     * Execute the console command.
     * 
     * Regra: Roda daily() (diariamente).
     * Lógica: Procura Evolucoes onde status='Rascunho' e created_at < (agora - 48h).
     * Ação: (Futuro) Dispara Notificação no Dashboard.
     */
    public function handle()
    {
        $limiteHoras = now()->subHours(48);

        $evolucoesPendentes = Evolucao::where('status', 'Rascunho')
            ->where('created_at', '<', $limiteHoras)
            ->with(['paciente', 'profissional'])
            ->get();

        if ($evolucoesPendentes->isEmpty()) {
            $this->info('Nenhuma evolução pendente encontrada.');
            return Command::SUCCESS;
        }

        $this->info("Encontradas {$evolucoesPendentes->count()} evolução(ões) pendente(s) há mais de 48 horas:");

        foreach ($evolucoesPendentes as $evolucao) {
            $this->line("  - Evolução #{$evolucao->id} - Paciente: {$evolucao->paciente->nome_completo} - Profissional: {$evolucao->profissional->name} - Criada em: {$evolucao->created_at->format('d/m/Y H:i')}");
        }

        // TODO: Futuro - Disparar notificações no Dashboard
        // Exemplo: Notification::send($evolucoesPendentes->pluck('profissional'), new EvolucaoPendenteNotification($evolucao));

        return Command::SUCCESS;
    }
}
