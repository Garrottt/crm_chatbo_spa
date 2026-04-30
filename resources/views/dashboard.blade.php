<x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="h-full overflow-y-auto bg-[#18181b] p-6 text-white md:p-8">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8 flex flex-col justify-between md:flex-row md:items-center">
            <div>
                <h1 class="mb-2 text-2xl font-bold tracking-tight">Estadisticas y Rendimiento</h1>
                <p class="text-sm text-gray-400">
                    Actual: {{ $periodLabels['current'] }} | Anterior: {{ $periodLabels['previous'] }}
                </p>
            </div>

            <div class="mt-4 flex items-center gap-6 md:mt-0">
                <select id="period-selector" onchange="window.location.href='?period='+this.value" class="mr-4 cursor-pointer rounded-lg border border-[#3f3f46] bg-[#27272a] px-3 py-1.5 text-sm outline-none focus:border-indigo-500">
                    <option value="este_mes" {{ $period == 'este_mes' ? 'selected' : '' }}>Este mes</option>
                    <option value="mes_pasado" {{ $period == 'mes_pasado' ? 'selected' : '' }}>Mes pasado</option>
                    <option value="ultimos_30" {{ $period == 'ultimos_30' ? 'selected' : '' }}>Ultimos 30 dias</option>
                    <option value="este_trimestre" {{ $period == 'este_trimestre' ? 'selected' : '' }}>Este trimestre</option>
                </select>

                <div class="text-right">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Ingresos</p>
                    <p class="text-lg font-bold leading-none">${{ number_format($stats['revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Citas</p>
                    <p class="text-lg font-bold leading-none">{{ $stats['appointments'] }}</p>
                </div>
                <div class="text-right">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Cancelacion</p>
                    <p class="text-lg font-bold leading-none">{{ number_format($stats['cancellationRate'], 1) }}%</p>
                </div>
                <div class="text-right">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Ticket prom.</p>
                    <p class="text-lg font-bold leading-none">${{ number_format($stats['avgTicket'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6 rounded-2xl border border-[#27272a] bg-[#1f1f22] p-5 shadow-lg">
            <div class="mb-2 flex items-center text-sm font-semibold text-gray-300">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                Ingresos ($)
            </div>
            <p class="mb-4 text-xs text-gray-500">
                Comparacion entre {{ $periodLabels['current'] }} y {{ $periodLabels['previous'] }}.
            </p>
            <div class="relative h-[300px] w-full">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-[#27272a] bg-[#1f1f22] p-6 shadow-lg">
                <h3 class="mb-6 text-sm font-bold text-white">Distribucion de Servicios</h3>
                <div class="relative flex h-[250px] w-full justify-center">
                    <canvas id="donutChart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-[#27272a] bg-[#1f1f22] p-6 shadow-lg">
                <h3 class="mb-6 text-sm font-bold text-white">Rendimiento Especialistas</h3>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-[#3f3f46]">
                                <th class="pb-3 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Especialista</th>
                                <th class="pb-3 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Citas</th>
                                <th class="pb-3 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($specialistsPerformance as $specialist)
                            <tr class="border-b border-[#27272a] transition-colors last:border-0 hover:bg-[#27272a]/50">
                                <td class="flex items-center gap-3 py-3 text-sm font-medium">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-[#3f3f46] text-[10px] text-white">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    {{ $specialist['name'] }}
                                </td>
                                <td class="py-3 text-sm text-gray-300">{{ $specialist['appointments'] }}</td>
                                <td class="py-3 text-sm font-bold">${{ number_format($specialist['revenue'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-sm text-gray-500">No hay datos en este periodo.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.color = '#a1a1aa';
    Chart.defaults.font.family = 'ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

    const lineCtx = document.getElementById('lineChart').getContext('2d');
    const lineChartData = @json($lineChartData);
    const periodLabels = @json($periodLabels);

    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: lineChartData.labels,
            datasets: [
                {
                    label: 'Periodo actual (' + periodLabels.current + ')',
                    data: lineChartData.data,
                    borderColor: '#93c5fd',
                    backgroundColor: 'rgba(147, 197, 253, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: false
                },
                {
                    label: 'Periodo anterior (' + periodLabels.previous + ')',
                    data: lineChartData.prevData,
                    borderColor: '#52525b',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.3,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#a1a1aa',
                        usePointStyle: true,
                        boxWidth: 8
                    }
                },
                tooltip: {
                    backgroundColor: '#27272a',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#3f3f46',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let value = context.parsed.y;
                            return context.dataset.label + ': $' + value.toLocaleString('es-CL');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { maxTicksLimit: 8 }
                },
                y: {
                    grid: { color: '#27272a', drawBorder: false },
                    border: { display: false },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000) return '$' + (value / 1000) + 'k';
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });

    const donutCtx = document.getElementById('donutChart').getContext('2d');
    const donutChartData = @json($donutChartData);
    const hasData = donutChartData.data.length > 0 && donutChartData.data.some(v => v > 0);

    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: hasData ? donutChartData.labels : ['Sin datos'],
            datasets: [{
                data: hasData ? donutChartData.data : [1],
                backgroundColor: hasData ? [
                    '#60a5fa',
                    '#fbbf24',
                    '#86efac',
                    '#c084fc',
                    '#f87171',
                    '#94a3b8'
                ] : ['#3f3f46'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: hasData,
                    backgroundColor: '#27272a',
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            label += context.parsed;
                            return label + ' citas';
                        }
                    }
                }
            }
        }
    });
});
</script>
</x-app-layout>
