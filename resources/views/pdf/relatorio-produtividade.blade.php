<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Produtividade</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2937; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #4f46e5; padding-bottom: 12px; margin-bottom: 20px; }
        .brand { font-size: 20px; font-weight: 700; color: #4f46e5; }
        .meta { text-align: right; font-size: 11px; color: #6b7280; }
        h2 { font-size: 18px; margin-bottom: 8px; color: #111827; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #eef2ff; color: #312e81; padding: 8px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        .summary { display: flex; gap: 16px; margin-top: 10px; }
        .card { flex: 1; padding: 12px; border-radius: 10px; color: white; }
        .indigo { background: linear-gradient(135deg, #4f46e5, #6366f1); }
        .rose { background: linear-gradient(135deg, #f43f5e, #fb7185); }
        .emerald { background: linear-gradient(135deg, #10b981, #34d399); }
        .card-title { text-transform: uppercase; font-size: 10px; letter-spacing: .08em; margin-bottom: 6px; opacity: .85; }
        .card-value { font-size: 20px; font-weight: 700; }
        footer { margin-top: 24px; font-size: 10px; text-align: center; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Equidade+ Saúde Integrada</div>
        <div class="meta">
            <div>Relatório de Produtividade</div>
            <div>Gerado em {{ now()->format('d/m/Y H:i') }}</div>
            <div>Período {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="summary">
        <div class="card indigo">
            <div class="card-title">Atendimentos concluídos</div>
            <div class="card-value">{{ $totalAtendimentos }}</div>
        </div>
        <div class="card rose">
            <div class="card-title">Cancelados / ausências</div>
            <div class="card-value">{{ $totalCancelados }}</div>
        </div>
        <div class="card emerald">
            <div class="card-title">Absenteísmo</div>
            <div class="card-value">{{ number_format($percentualAbsenteismo, 1, ',', '.') }}%</div>
        </div>
    </div>

    <h2>Produtividade por profissional</h2>
    <table>
        <thead>
            <tr>
                <th>Profissional</th>
                <th>Total de atendimentos</th>
                <th>Dias trabalhados</th>
                <th>Média diária</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dados as $item)
                <tr>
                    <td>{{ $item['profissional_nome'] }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td>{{ $item['dias_trabalhados'] }}</td>
                    <td>{{ number_format($item['media_diaria'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhum atendimento concluído no período informado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        Relatório gerado automaticamente pelo Equidade+ • {{ now()->format('d/m/Y') }}
    </footer>
</body>
</html>
