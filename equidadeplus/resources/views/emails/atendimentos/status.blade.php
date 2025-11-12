<div style="font-family: Arial, sans-serif; color:#1f2937;">
    <h2 style="color:#4f46e5; margin-bottom:16px;">Atualização do atendimento</h2>
    <p style="margin-bottom:12px;">
        O atendimento agendado para <strong>{{ $atendimento->data_hora_inicio->format('d/m/Y H:i') }}</strong>
        foi <strong>{{ strtolower($status) }}</strong>.
    </p>

    <ul style="padding-left:16px; margin-bottom:16px; color:#374151;">
        <li><strong>Paciente:</strong> {{ $atendimento->paciente->nome_completo ?? 'N/A' }}</li>
        <li><strong>Profissional:</strong> {{ $atendimento->profissional->name ?? 'N/A' }}</li>
        <li><strong>Sala:</strong> {{ $atendimento->sala->nome ?? 'Não informada' }}</li>
        <li><strong>Início:</strong> {{ $atendimento->data_hora_inicio->format('d/m/Y H:i') }}</li>
        <li><strong>Término:</strong> {{ $atendimento->data_hora_fim->format('d/m/Y H:i') }}</li>
    </ul>

    <p style="margin-bottom:8px;">
        Caso precise fazer ajustes, acesse o painel Equidade+ e abra a agenda.
    </p>

    <p style="color:#6b7280; font-size:12px; margin-top:24px;">
        Esta é uma mensagem automática. Dúvidas? Entre em contato com a equipe da clínica.
    </p>
</div>

