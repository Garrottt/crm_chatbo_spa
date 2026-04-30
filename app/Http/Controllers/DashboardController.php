<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $period = $request->get('period', 'este_mes');
        $now = Carbon::now();

        [$start, $end, $prevStart, $prevEnd] = $this->resolvePeriods($period, $now);

        $bookings = Booking::with(['service', 'specialist'])
            ->whereBetween('scheduledAt', [$start, $end])
            ->get();

        $prevBookings = Booking::with(['service'])
            ->whereBetween('scheduledAt', [$prevStart, $prevEnd])
            ->get();

        $appointments = $bookings->count();
        $cancellationsCount = $bookings->filter(function ($booking) {
            return strtoupper($booking->status) === 'CANCELLED';
        })->count();

        $cancellationRate = $appointments > 0 ? ($cancellationsCount / $appointments) * 100 : 0;

        $completed = $bookings->filter(function ($booking) {
            return strtoupper($booking->status) !== 'CANCELLED';
        });

        $prevCompleted = $prevBookings->filter(function ($booking) {
            return strtoupper($booking->status) !== 'CANCELLED';
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

        $lineChartData = [
            'labels' => [],
            'data' => [],
            'prevData' => [],
        ];

        $currentDate = $start->copy()->startOfDay();
        $currentEndDate = $end->copy()->startOfDay();
        $dailyRevenues = [];
        $dayOffsets = [];
        $idx = 0;

        while ($currentDate <= $currentEndDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dailyRevenues[$dateString] = 0;
            $dayOffsets[$idx] = $dateString;
            $idx++;
            $currentDate->addDay();
        }

        foreach ($completed as $booking) {
            $dateString = Carbon::parse($booking->scheduledAt)->format('Y-m-d');
            if (array_key_exists($dateString, $dailyRevenues)) {
                $dailyRevenues[$dateString] += ($booking->service ? $booking->service->price : 0);
            }
        }

        $prevDailyRevenues = array_fill(0, count($dayOffsets), 0);
        foreach ($prevCompleted as $booking) {
            $bookingDate = Carbon::parse($booking->scheduledAt);
            $diffInDays = $prevStart->copy()->startOfDay()->diffInDays($bookingDate->copy()->startOfDay());

            if (isset($prevDailyRevenues[$diffInDays])) {
                $prevDailyRevenues[$diffInDays] += ($booking->service ? $booking->service->price : 0);
            }
        }

        foreach ($dayOffsets as $i => $dateString) {
            $lineChartData['labels'][] = Carbon::parse($dateString)->translatedFormat('d M');
            $lineChartData['data'][] = $dailyRevenues[$dateString];
            $lineChartData['prevData'][] = $prevDailyRevenues[$i] ?? 0;
        }

        $serviceDistribution = [];
        foreach ($completed as $booking) {
            if (!$booking->service) {
                continue;
            }

            $name = $booking->service->name;
            if (!isset($serviceDistribution[$name])) {
                $serviceDistribution[$name] = 0;
            }

            $serviceDistribution[$name]++;
        }

        $donutChartData = [
            'labels' => array_keys($serviceDistribution),
            'data' => array_values($serviceDistribution),
        ];

        $specialistsPerformance = [];
        foreach ($completed as $booking) {
            if (!$booking->specialist) {
                continue;
            }

            $id = $booking->specialist->id;
            $name = $booking->specialist->name ?? 'Especialista';

            if (!isset($specialistsPerformance[$id])) {
                $specialistsPerformance[$id] = [
                    'name' => $name,
                    'appointments' => 0,
                    'revenue' => 0,
                ];
            }

            $specialistsPerformance[$id]['appointments']++;
            $specialistsPerformance[$id]['revenue'] += ($booking->service ? $booking->service->price : 0);
        }

        $specialistsPerformance = array_values($specialistsPerformance);
        usort($specialistsPerformance, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        $periodLabels = [
            'current' => $this->formatPeriodLabel($start, $end),
            'previous' => $this->formatPeriodLabel($prevStart, $prevEnd),
        ];

        return view('dashboard', compact(
            'stats',
            'period',
            'lineChartData',
            'donutChartData',
            'specialistsPerformance',
            'periodLabels'
        ));
    }

    private function resolvePeriods(string $period, Carbon $now): array
    {
        switch ($period) {
            case 'mes_pasado':
                $start = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $end = $now->copy()->subMonthNoOverflow()->endOfMonth();
                $prevStart = $start->copy()->subMonthNoOverflow()->startOfMonth();
                $prevEnd = $start->copy()->subMonthNoOverflow()->endOfMonth();
                break;

            case 'ultimos_30':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDays(30)->startOfDay();
                $prevEnd = $start->copy()->subDay()->endOfDay();
                break;

            case 'este_trimestre':
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy();
                $prevStart = $start->copy()->subQuarter()->startOfQuarter();
                $prevEnd = $prevStart->copy()->addSeconds($start->diffInSeconds($end));
                break;

            case 'este_mes':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy();
                $prevStart = $start->copy()->subMonthNoOverflow()->startOfMonth();
                $prevEnd = $prevStart->copy()->addSeconds($start->diffInSeconds($end));
                break;
        }

        return [$start, $end, $prevStart, $prevEnd];
    }

    private function formatPeriodLabel(Carbon $start, Carbon $end): string
    {
        if ($start->isSameDay($end)) {
            return $start->translatedFormat('d M Y');
        }

        return $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
    }
}
