<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Frequência</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 9px;
            color: #666;
        }
        .filtros {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 9px;
        }
        .filtros strong {
            color: #4f46e5;
        }
        .resumo {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .card {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 15px;
        }
        .card-title {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .card-value {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        thead {
            background: #4f46e5;
            color: white;
        }
        th {
            padding: 10px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        tbody tr:hover {
            background: #f9fafb;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Frequência</h1>
        <div class="header-info">
            <div>
                <strong>Gerado por:</strong> {{ $usuario->name }}<br>
                <strong>Data/Hora:</strong> {{ $geradoEm }}
            </div>
            <div>
                <strong>Sistema:</strong> {{ config('app.name', 'Equidade') }}
            </div>
        </div>
    </div>

    <div class="filtros">
        <strong>Período:</strong> {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
        @if($pacienteSelecionado)
            | <strong>Paciente:</strong> {{ $pacienteSelecionado }}
        @endif
        @if($unidadeSelecionada)
            | <strong>Unidade:</strong> {{ $unidadeSelecionada }}
        @endif
    </div>

    <div class="resumo">
        <div class="card">
            <div class="card-title">Total Concluídos</div>
            <div class="card-value" style="color: #059669;">{{ $totalConcluidos }}</div>
        </div>
        <div class="card">
            <div class="card-title">Total Cancelados</div>
            <div class="card-value" style="color: #dc2626;">{{ $totalCanceladosFreq }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>CPF</th>
                <th>Total</th>
                <th>Concluídos</th>
                <th>Cancelados</th>
                <th>Taxa de Presença</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dadosFrequencia as $item)
                <tr>
                    <td>{{ $item['paciente']->nome_completo }}</td>
                    <td>{{ $item['paciente']->cpf ?? 'N/A' }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td style="color: #059669; font-weight: bold;">{{ $item['concluidos'] }}</td>
                    <td style="color: #dc2626; font-weight: bold;">{{ $item['cancelados'] }}</td>
                    <td>
                        @php
                            $taxa = $item['taxa_presenca'];
                            $badgeClass = $taxa >= 80 ? 'badge-success' : ($taxa >= 60 ? 'badge-warning' : 'badge-danger');
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ number_format($taxa, 1) }}%
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #6b7280;">
                        Nenhum dado encontrado para o período selecionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo sistema {{ config('app.name', 'Equidade') }}</p>
        <p>© {{ date('Y') }} {{ config('app.name', 'Equidade') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>

