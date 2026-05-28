<x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="h-full overflow-y-auto bg-[linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] p-4 pb-24 text-slate-900 sm:p-6 md:p-8 md:pb-8">
    <div class="mx-auto max-w-6xl">
        <div class="mb-6 grid gap-5 lg:mb-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,520px)_auto] lg:items-center">
            <div class="min-w-0">
                <h1 class="mb-2 text-2xl font-bold tracking-tight">Estadisticas y Rendimiento</h1>
                <p class="text-sm font-medium text-slate-500">
                    Actual: {{ $periodLabels['current'] }} | Anterior: {{ $periodLabels['previous'] }}
                </p>
            </div>

            <div class="grid items-center gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                <form id="month-filter-form" method="GET" action="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <input
                        id="report-month-input"
                        type="month"
                        name="report_month"
                        value="{{ $reportMonth }}"
                        aria-label="Mes de estadisticas"
                        class="min-w-0 flex-1 rounded-xl border-0 bg-transparent px-2 py-2 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-100"
                    >
                    <button type="submit" class="shrink-0 rounded-xl bg-slate-100 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-slate-700 transition hover:bg-slate-200">
                        Ver
                    </button>
                </form>

                <form method="GET" action="{{ route('dashboard.report.pdf') }}" class="flex min-w-0 items-center">
                    <input id="pdf-report-month-input" type="hidden" name="report_month" value="{{ $reportMonth }}">
                    <button type="submit" class="inline-flex h-[54px] shrink-0 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 text-xs font-black uppercase tracking-[0.14em] text-white shadow-sm transition hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 3v5h5M9 15h6M9 18h4" />
                        </svg>
                        <span class="hidden sm:inline">Exportar PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:flex lg:items-center lg:gap-6">
                <div class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-left shadow-sm sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none sm:text-right">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Ingresos</p>
                    <p class="text-lg font-bold leading-none">${{ number_format($stats['revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-left shadow-sm sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none sm:text-right">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Citas</p>
                    <p class="text-lg font-bold leading-none">{{ $stats['appointments'] }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-left shadow-sm sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none sm:text-right">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Cancelacion</p>
                    <p class="text-lg font-bold leading-none">{{ number_format($stats['cancellationRate'], 1) }}%</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-left shadow-sm sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none sm:text-right">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Ticket prom.</p>
                    <p class="text-lg font-bold leading-none">${{ number_format($stats['avgTicket'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6 rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/70">
            <div class="mb-2 flex items-center text-sm font-semibold text-slate-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                Ingresos ($)
            </div>
            <p class="mb-4 text-xs font-medium text-slate-500">
                Comparacion entre {{ $periodLabels['current'] }} y {{ $periodLabels['previous'] }}.
            </p>
            <div class="relative h-[300px] w-full">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/70">
                <h3 class="mb-6 text-sm font-bold text-slate-900">Distribucion de Servicios</h3>
                @php
                    $serviceChartColors = ['#60a5fa', '#fbbf24', '#86efac', '#c084fc', '#f87171', '#94a3b8'];
                    $hasServiceDistribution = count($donutChartData['labels']) > 0 && collect($donutChartData['data'])->contains(fn ($value) => $value > 0);
                @endphp
                <div class="grid items-center gap-6 lg:grid-cols-[minmax(0,1fr)_220px]">
                    <div class="relative flex h-[250px] w-full justify-center">
                        <canvas id="donutChart"></canvas>
                    </div>

                    <div class="space-y-3">
                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400"></p>
                        @if($hasServiceDistribution)
                            @foreach($donutChartData['labels'] as $index => $label)
                                <div class="flex items-start gap-3">
                                    <span class="mt-1 h-3 w-3 shrink-0 rounded-full" style="background-color: {{ $serviceChartColors[$index % count($serviceChartColors)] }}"></span>
                                    <div class="min-w-0">
                                        <p class="break-words text-sm font-bold text-slate-800">{{ $label }}</p>
                                        <p class="text-xs font-medium text-slate-500">{{ $donutChartData['data'][$index] ?? 0 }} citas</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-3 w-3 shrink-0 rounded-full bg-slate-200"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Sin datos</p>
                                    <p class="text-xs font-medium text-slate-500">No hay citas en este mes.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/70">
                <h3 class="mb-6 text-sm font-bold text-slate-900">Rendimiento Especialistas</h3>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="pb-3 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Especialista</th>
                                <th class="pb-3 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Citas</th>
                                <th class="pb-3 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($specialistsPerformance as $specialist)
                            <tr class="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50">
                                <td class="flex items-center gap-3 py-3 text-sm font-medium text-slate-800">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-50 text-[10px] text-indigo-600">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    {{ $specialist['name'] }}
                                </td>
                                <td class="py-3 text-sm text-slate-600">{{ $specialist['appointments'] }}</td>
                                <td class="py-3 text-sm font-bold text-slate-900">${{ number_format($specialist['revenue'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-sm text-slate-500">No hay datos en este periodo.</td>
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
    const reportMonthInput = document.getElementById('report-month-input');
    const pdfReportMonthInput = document.getElementById('pdf-report-month-input');

    reportMonthInput?.addEventListener('change', function() {
        if (pdfReportMonthInput) {
            pdfReportMonthInput.value = this.value;
        }
    });

    Chart.defaults.color = '#64748b';
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
                    backgroundColor: 'rgba(147, 197, 253, 0.12)',
                    borderWidth: 3,
                    tension: 0.3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: false
                },
                {
                    label: 'Periodo anterior (' + periodLabels.previous + ')',
                    data: lineChartData.prevData,
                    borderColor: '#94a3b8',
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
                        color: '#64748b',
                        usePointStyle: true,
                        boxWidth: 8
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#1e293b',
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
                    grid: { color: '#e2e8f0', drawBorder: false },
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
                ] : ['#e2e8f0'],
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
                    backgroundColor: '#0f172a',
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
