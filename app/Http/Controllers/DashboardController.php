<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Specialist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'este_mes');
        $now = Carbon::now();
        $start = null;
        $end = null;
        $prevStart = null;
        $prevEnd = null;

        switch ($period) {
            case 'mes_pasado':
                $start = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $end = $now->copy()->subMonthNoOverflow()->endOfMonth();
                $prevStart = $start->copy()->subMonthNoOverflow()->startOfMonth();
                $prevEnd = $start->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'ultimos_30':
                $start = $now->copy()->subDays(30)->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDays(30)->startOfDay();
                $prevEnd = $start->copy()->subDays(1)->endOfDay();
                break;
            case 'este_trimestre':
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
                $prevStart = $start->copy()->subQuarter()->startOfQuarter();
                $prevEnd = $start->copy()->subQuarter()->endOfQuarter();
                break;
            case 'este_mes':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $prevStart = $start->copy()->subMonthNoOverflow()->startOfMonth();
                $prevEnd = $start->copy()->subMonthNoOverflow()->endOfMonth();
                break;
        }

        // Todos los bookings del rango actual
        $bookings = Booking::with(['service', 'specialist'])
            ->whereBetween('scheduledAt', [$start, $end])
            ->get();

        // Todos los bookings del rango anterior para comparar
        $prevBookings = Booking::with(['service'])
            ->whereBetween('scheduledAt', [$prevStart, $prevEnd])
            ->get();

        // Calcular Stats principales
        $appointments = $bookings->count();
        $cancellationsCount = $bookings->filter(function ($b) {
            return strtoupper($b->status) === 'CANCELLED';
        })->count();

        $cancellationRate = $appointments > 0 ? ($cancellationsCount / $appointments) * 100 : 0;

        $completed = $bookings->filter(function ($b) {
            return strtoupper($b->status) !== 'CANCELLED';
        });

        $prevCompleted = $prevBookings->filter(function ($b) {
            return strtoupper($b->status) !== 'CANCELLED';
        });

        $revenue = $completed->reduce(function ($carry, $booking) {
            return $carry + ($booking->service ? $booking->service->price : 0);
        }, 0);

        $avgTicket = $completed->count() > 0 ? $revenue / $completed->count() : 0;

        $stats = [
            'revenue' => $revenue,
            'appointments' => $appointments,
            'cancellationRate' => $cancellationRate,
            'avgTicket' => $avgTicket,
        ];

        // 1. Datos para Line Chart (Ingresos diarios y comparativa)
        $lineChartData = [
            'labels' => [],
            'data' => [],
            'prevData' => []
        ];

        // Rellenar todos los días del rango para que la gráfica tenga continuidad
        $currentDate = $start->copy();
        $dailyRevenues = [];
        $dayOffsets = [];
        $idx = 0;
        
        while ($currentDate <= $end) {
            $dateString = $currentDate->format('Y-m-d');
            $dailyRevenues[$dateString] = 0;
            $dayOffsets[$idx] = $dateString;
            $idx++;
            $currentDate->addDay();
        }

        foreach ($completed as $booking) {
            $dateString = Carbon::parse($booking->scheduledAt)->format('Y-m-d');
            if (isset($dailyRevenues[$dateString])) {
                $dailyRevenues[$dateString] += ($booking->service ? $booking->service->price : 0);
            }
        }

        // Calcular ingresos por día del mes anterior alineados por índice (día 1 vs día 1)
        $prevDailyRevenues = array_fill(0, count($dayOffsets), 0);
        foreach ($prevCompleted as $booking) {
            $bookingDate = Carbon::parse($booking->scheduledAt);
            // Encontrar el offset de días respecto al inicio del periodo anterior
            $diffInDays = $prevStart->copy()->startOfDay()->diffInDays($bookingDate->copy()->startOfDay());
            if (isset($prevDailyRevenues[$diffInDays])) {
                $prevDailyRevenues[$diffInDays] += ($booking->service ? $booking->service->price : 0);
            }
        }

        foreach ($dayOffsets as $i => $dateString) {
            $lineChartData['labels'][] = Carbon::parse($dateString)->format('d M');
            $lineChartData['data'][] = $dailyRevenues[$dateString];
            $lineChartData['prevData'][] = $prevDailyRevenues[$i] ?? 0;
        }

        // 2. Datos para Donut Chart (Distribución de Servicios)
        $serviceDistribution = [];
        foreach ($completed as $booking) {
            if ($booking->service) {
                $name = $booking->service->name;
                if (!isset($serviceDistribution[$name])) {
                    $serviceDistribution[$name] = 0;
                }
                $serviceDistribution[$name]++;
            }
        }

        $donutChartData = [
            'labels' => array_keys($serviceDistribution),
            'data' => array_values($serviceDistribution)
        ];

        // 3. Tabla de Rendimiento de Especialistas
        $specialistsPerformance = [];
        foreach ($completed as $booking) {
            if ($booking->specialist) {
                $id = $booking->specialist->id;
                $name = $booking->specialist->name ?? 'Especialista';
                
                if (!isset($specialistsPerformance[$id])) {
                    $specialistsPerformance[$id] = [
                        'name' => $name,
                        'appointments' => 0,
                        'revenue' => 0
                    ];
                }
                
                $specialistsPerformance[$id]['appointments']++;
                $specialistsPerformance[$id]['revenue'] += ($booking->service ? $booking->service->price : 0);
            }
        }
        
        // Ordenar por ingresos descendente
        usort($specialistsPerformance, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return view('dashboard', compact('stats', 'period', 'lineChartData', 'donutChartData', 'specialistsPerformance'));
    }
}
