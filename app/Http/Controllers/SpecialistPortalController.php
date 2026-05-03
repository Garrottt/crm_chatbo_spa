<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Specialist;
use Carbon\Carbon;
use Illuminate\View\View;

class SpecialistPortalController extends Controller
{
    private const SPA_TIMEZONE = 'America/Santiago';

    private const DAYS = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miercoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sabado',
        0 => 'Domingo',
    ];

    public function index(): View
    {
        $user = auth()->user();
        abort_unless($user && $user->isSpecialist(), 403);

        $specialist = Specialist::with(['user', 'services', 'availabilities'])
            ->where('userId', $user->id)
            ->firstOrFail();

        $now = Carbon::now(self::SPA_TIMEZONE);
        $todayStart = $now->copy()->startOfDay()->utc();
        $todayEnd = $now->copy()->endOfDay()->utc();
        $weekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay()->utc();
        $nowUtc = $now->copy()->utc();

        $todayBookings = Booking::with(['client', 'service'])
            ->where('specialistId', $specialist->id)
            ->whereBetween('scheduledAt', [$todayStart, $todayEnd])
            ->orderBy('scheduledAt')
            ->get();

        $upcomingBookings = Booking::with(['client', 'service'])
            ->where('specialistId', $specialist->id)
            ->where('scheduledAt', '>=', $nowUtc)
            ->where('status', '!=', 'CANCELLED')
            ->orderBy('scheduledAt')
            ->limit(8)
            ->get();

        $weekBookings = Booking::with(['client', 'service'])
            ->where('specialistId', $specialist->id)
            ->where('status', '!=', 'CANCELLED')
            ->whereBetween('scheduledAt', [$todayStart, $weekEnd])
            ->orderBy('scheduledAt')
            ->get();

        $occupiedMinutesToday = $todayBookings
            ->where('status', '!=', 'CANCELLED')
            ->reduce(function ($total, Booking $booking) {
                if (!$booking->scheduledAt || !$booking->endAt) {
                    return $total;
                }

                return $total + $booking->scheduledAt->diffInMinutes($booking->endAt);
            }, 0);

        $stats = [
            'services' => $specialist->services->count(),
            'today' => $todayBookings->count(),
            'pending' => $todayBookings->where('status', 'PENDING')->count(),
            'week' => $weekBookings->count(),
        ];

        $todaySummary = [
            'appointments' => $todayBookings->count(),
            'occupiedHours' => $this->formatOccupiedHours($occupiedMinutesToday),
            'statusLabel' => $todayBookings->where('status', 'PENDING')->count() > 0 ? 'Con pendientes' : 'Sin pendientes',
            'statusHint' => $todayBookings->where('status', 'PENDING')->count() > 0 ? 'Revise las confirmaciones' : 'Todo al dia',
        ];

        $todaySummary = [
            'appointments' => $todayBookings->count(),
            'occupiedHours' => $this->formatOccupiedHours($occupiedMinutesToday),
            'statusLabel' => $todayBookings->where('status', 'PENDING')->count() > 0 ? 'Con pendientes' : 'Sin pendientes',
            'statusHint' => $todayBookings->where('status', 'PENDING')->count() > 0 ? 'Revise las confirmaciones' : 'Todo al dia',
        ];

        $availability = collect(self::DAYS)->map(function ($label, $day) use ($specialist) {
            $slot = $specialist->availabilities->firstWhere('dayOfWeek', $day);

            return [
                'day' => $label,
                'enabled' => (bool) $slot,
                'hours' => $slot
                    ? substr((string) $slot->startTime, 0, 5) . ' - ' . substr((string) $slot->endTime, 0, 5)
                    : 'Sin atencion',
            ];
        })->values();

        return view('specialists.portal', [
            'specialist' => $specialist,
            'stats' => $stats,
            'todaySummary' => $todaySummary,
            'todayBookings' => $todayBookings,
            'upcomingBookings' => $upcomingBookings,
            'availability' => $availability,
            'nextBooking' => $upcomingBookings->first(),
        ]);
    }

    private function formatOccupiedHours(int $minutes): string
    {
        if ($minutes <= 0) {
            return '0 h';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return $hours . ' h';
        }

        return sprintf('%d h %02d', $hours, $remainingMinutes);
    }
}
