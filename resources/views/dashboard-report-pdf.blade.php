<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte mensual - {{ $reportMonth }}</title>
    <style>
        body {
            margin: 0;
            color: #0f172a;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }

        .page {
            padding: 28px;
        }

        .header {
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 22px;
            padding-bottom: 16px;
        }

        .eyebrow {
            color: #64748b;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2.6px;
            text-transform: uppercase;
        }

        h1 {
            margin: 6px 0 4px;
            font-size: 24px;
            line-height: 1.15;
        }

        h2 {
            margin: 0 0 10px;
            color: #0f172a;
            font-size: 14px;
        }

        .muted {
            color: #64748b;
        }

        .stats {
            display: table;
            margin-bottom: 20px;
            width: 100%;
            table-layout: fixed;
        }

        .stat {
            display: table-cell;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
        }

        .stat + .stat {
            border-left: 0;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        .stat-value {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 800;
        }

        .section {
            margin-top: 18px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #f8fafc;
            color: #64748b;
            font-size: 9px;
            letter-spacing: 1.6px;
            text-align: left;
            text-transform: uppercase;
        }

        th,
        td {
            border-bottom: 1px solid #e2e8f0;
            padding: 9px 8px;
        }

        .right {
            text-align: right;
        }

        .empty {
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            color: #64748b;
            padding: 18px;
            text-align: center;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 10px;
            margin-top: 24px;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="eyebrow">Reporte mensual</div>
            <h1>Estadisticas y rendimiento</h1>
            <div class="muted">
                Mes seleccionado: {{ ucfirst($reportMonth) }}<br>
                Comparacion: {{ $periodLabels['current'] }} vs {{ $periodLabels['previous'] }}
            </div>
        </div>

        <div class="stats">
            <div class="stat">
                <div class="stat-label">Ingresos</div>
                <div class="stat-value">${{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Citas</div>
                <div class="stat-value">{{ $stats['appointments'] }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Cancelacion</div>
                <div class="stat-value">{{ number_format($stats['cancellationRate'], 1) }}%</div>
            </div>
            <div class="stat">
                <div class="stat-label">Ticket prom.</div>
                <div class="stat-value">${{ number_format($stats['avgTicket'], 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="section">
            <h2>Ingresos diarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>Dia</th>
                        <th class="right">Periodo actual</th>
                        <th class="right">Periodo anterior</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lineChartData['labels'] as $index => $label)
                        <tr>
                            <td>{{ $label }}</td>
                            <td class="right">${{ number_format($lineChartData['data'][$index] ?? 0, 0, ',', '.') }}</td>
                            <td class="right">${{ number_format($lineChartData['prevData'][$index] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Distribucion de servicios</h2>
            @if(count($donutChartData['labels']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th class="right">Citas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donutChartData['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="right">{{ $donutChartData['data'][$index] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">No hay servicios registrados en este mes.</div>
            @endif
        </div>

        <div class="section">
            <h2>Rendimiento de especialistas</h2>
            @if(count($specialistsPerformance) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Especialista</th>
                            <th class="right">Citas</th>
                            <th class="right">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($specialistsPerformance as $specialist)
                            <tr>
                                <td>{{ $specialist['name'] }}</td>
                                <td class="right">{{ $specialist['appointments'] }}</td>
                                <td class="right">${{ number_format($specialist['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">No hay rendimiento de especialistas en este mes.</div>
            @endif
        </div>

        <div class="footer">
            Generado el {{ $generatedAt->timezone('America/Santiago')->format('d/m/Y H:i') }} desde el CRM.
        </div>
    </div>
</body>
</html>
