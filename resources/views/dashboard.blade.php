<x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="h-full overflow-y-auto bg-[#18181b] p-6 md:p-8 text-white">
    <div class="mx-auto max-w-6xl">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <h1 class="text-2xl font-bold tracking-tight mb-4 md:mb-0">Estadísticas y Rendimiento</h1>
            
            <div class="flex items-center gap-6">
                <!-- Select for period (optional for functionality, styled dark) -->
                <select id="period-selector" onchange="window.location.href='?period='+this.value" class="bg-[#27272a] border border-[#3f3f46] text-sm rounded-lg px-3 py-1.5 outline-none focus:border-indigo-500 cursor-pointer mr-4">
                    <option value="este_mes" {{ $period == 'este_mes' ? 'selected' : '' }}>Este Mes</option>
                    <option value="mes_pasado" {{ $period == 'mes_pasado' ? 'selected' : '' }}>Mes Pasado</option>
                    <option value="ultimos_30" {{ $period == 'ultimos_30' ? 'selected' : '' }}>Últimos 30 días</option>
                    <option value="este_trimestre" {{ $period == 'este_trimestre' ? 'selected' : '' }}>Este Trimestre</option>
                </select>

                <div class="text-right">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold mb-1">Ingresos</p>
                    <p class="font-bold text-lg leading-none">${{ number_format($stats['revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold mb-1">Citas</p>
                    <p class="font-bold text-lg leading-none">{{ $stats['appointments'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold mb-1">Cancelación</p>
                    <p class="font-bold text-lg leading-none">{{ number_format($stats['cancellationRate'], 1) }}%</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold mb-1">Ticket Prom.</p>
                    <p class="font-bold text-lg leading-none">${{ number_format($stats['avgTicket'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Line Chart -->
        <div class="bg-[#1f1f22] border border-[#27272a] rounded-2xl p-5 mb-6 shadow-lg">
            <div class="flex items-center text-sm font-semibold mb-4 text-gray-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                Ingresos ($)
            </div>
            <div class="relative h-[300px] w-full">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <!-- Bottom Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Donut Chart -->
            <div class="bg-[#1f1f22] border border-[#27272a] rounded-2xl p-6 shadow-lg">
                <h3 class="font-bold text-sm mb-6 text-white">Distribución de Servicios</h3>
                <div class="relative h-[250px] w-full flex justify-center">
                    <canvas id="donutChart"></canvas>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-[#1f1f22] border border-[#27272a] rounded-2xl p-6 shadow-lg">
                <h3 class="font-bold text-sm mb-6 text-white">Rendimiento Especialistas</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#3f3f46]">
                                <th class="pb-3 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Especialista</th>
                                <th class="pb-3 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Citas</th>
                                <th class="pb-3 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($specialistsPerformance as $specialist)
                            <tr class="border-b border-[#27272a] last:border-0 hover:bg-[#27272a]/50 transition-colors">
                                <td class="py-3 text-sm flex items-center gap-3 font-medium">
                                    <div class="w-6 h-6 rounded-full bg-[#3f3f46] flex items-center justify-center text-[10px] text-white">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    {{ $specialist['name'] }}
                                </td>
                                <td class="py-3 text-sm text-gray-300">{{ $specialist['appointments'] }}</td>
                                <td class="py-3 text-sm font-bold">${{ number_format($specialist['revenue'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-sm text-gray-500">No hay datos en este período.</td>
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
    // Shared Options
    Chart.defaults.color = '#a1a1aa';
    Chart.defaults.font.family = 'ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

    // 1. Line Chart Setup
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    const lineChartData = @json($lineChartData);
    
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: lineChartData.labels,
            datasets: [
                {
                    label: 'Período Actual',
                    data: lineChartData.data,
                    borderColor: '#93c5fd', // Light blue line
                    backgroundColor: 'rgba(147, 197, 253, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: false
                },
                {
                    label: 'Período Anterior',
                    data: lineChartData.prevData,
                    borderColor: '#52525b', // Zinc/Gray line
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

    // 2. Donut Chart Setup
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    const donutChartData = @json($donutChartData);
    
    // Si no hay datos, mostrar algo por defecto
    const hasData = donutChartData.data.length > 0 && donutChartData.data.some(v => v > 0);
    
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: hasData ? donutChartData.labels : ['Sin datos'],
            datasets: [{
                data: hasData ? donutChartData.data : [1],
                backgroundColor: hasData ? [
                    '#60a5fa', // Blue
                    '#fbbf24', // Yellow/Orange
                    '#86efac', // Light Green
                    '#c084fc', // Purple
                    '#f87171', // Red
                    '#94a3b8'  // Slate
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
