<div style="font-family: Arial, sans-serif; color:#1f2937;">
    <h2 style="color:#4f46e5; margin-bottom:16px;">Evolução pendente para revisão</h2>

    <p style="margin-bottom:12px;">
        Foi criada uma evolução pendente para o paciente
        <strong>{{ $evolucao->paciente->nome_completo ?? 'Paciente' }}</strong>
        após o atendimento realizado em
        <strong>{{ optional($evolucao->atendimento)->data_hora_inicio?->format('d/m/Y H:i') ?? '--' }}</strong>.
    </p>

    <p style="margin-bottom:12px;">
        Acesse o painel do Equidade+ para completar o relato clínico e finalizar o registro.
    </p>

    <ul style="padding-left:16px; margin-bottom:16px; color:#374151;">
        <li><strong>Paciente:</strong> {{ $evolucao->paciente->nome_completo ?? 'N/A' }}</li>
        <li><strong>Profissional responsável:</strong> {{ $evolucao->profissional->name ?? 'N/A' }}</li>
        <li><strong>Status:</strong> {{ $evolucao->status }}</li>
    </ul>

    <p style="color:#6b7280; font-size:12px; margin-top:24px;">
        Esta é uma mensagem automática. Caso não reconheça esta evolução, entre em contato com a coordenação.
    </p>
</div>

