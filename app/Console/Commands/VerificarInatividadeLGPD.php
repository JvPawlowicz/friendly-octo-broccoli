<?php

namespace App\Console\Commands;

use App\Models\Paciente;
use Illuminate\Console\Command;

class VerificarInatividadeLGPD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lgpd:verificar-inatividade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica pacientes inativos há mais de 2 anos para revisão LGPD (Item 11)';

    /**
     * Execute the console command.
     * 
     * Regra: Roda monthly() (mensalmente).
     * Lógica: Procura Pacientes (status 'Ativo') que não têm Atendimentos nos últimos (ex: 2 anos).
     * Ação: (Futuro) Notifica Admin para revisão (Item 11).
     */
    public function handle()
    {
        $limiteAnos = now()->subYears(2);

        // Busca pacientes ativos que não têm atendimentos recentes
        $pacientesInativos = Paciente::where('status', 'Ativo')
            ->whereDoesntHave('atendimentos', function ($query) use ($limiteAnos) {
                $query->where('created_at', '>=', $limiteAnos);
            })
            ->get();

        if ($pacientesInativos->isEmpty()) {
            $this->info('Nenhum paciente inativo encontrado.');
            return Command::SUCCESS;
        }

        $this->info("Encontrados {$pacientesInativos->count()} paciente(s) inativo(s) há mais de 2 anos:");

        foreach ($pacientesInativos as $paciente) {
            $ultimoAtendimento = $paciente->atendimentos()
                ->orderBy('created_at', 'desc')
                ->first();

            $dataUltimoAtendimento = $ultimoAtendimento 
                ? $ultimoAtendimento->created_at->format('d/m/Y')
                : 'Nunca';

            $this->line("  - {$paciente->nome_completo} (ID: {$paciente->id}) - Último atendimento: {$dataUltimoAtendimento}");
        }

        // TODO: Futuro - Notificar Admin para revisão LGPD
        // Exemplo: Notification::send(Admin::all(), new PacienteInativoLGPDNotification($pacientesInativos));

        return Command::SUCCESS;
    }
}
