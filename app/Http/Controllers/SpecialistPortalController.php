<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Specialist;
use Carbon\Carbon;
use Illuminate\View\View;

class SpecialistPortalController extends Controller
{
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

        $specialist = Specialist::with(['services', 'availabilities'])
            ->where('userId', $user->id)
            ->firstOrFail();

        $now = Carbon::now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $weekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $todayBookings = Booking::with(['client', 'service'])
            ->where('specialistId', $specialist->id)
            ->whereBetween('scheduledAt', [$todayStart, $todayEnd])
            ->orderBy('scheduledAt')
            ->get();

        $upcomingBookings = Booking::with(['client', 'service'])
            ->where('specialistId', $specialist->id)
            ->where('scheduledAt', '>=', $now)
            ->where('status', '!=', 'CANCELLED')
            ->orderBy('scheduledAt')
            ->limit(8)
            ->get();

        $weekBookingsCount = Booking::query()
            ->where('specialistId', $specialist->id)
            ->where('status', '!=', 'CANCELLED')
            ->whereBetween('scheduledAt', [$todayStart, $weekEnd])
            ->count();

        $stats = [
            'services' => $specialist->services->count(),
            'today' => $todayBookings->count(),
            'pending' => $todayBookings->where('status', 'PENDING')->count(),
            'week' => $weekBookingsCount,
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
            'todayBookings' => $todayBookings,
            'upcomingBookings' => $upcomingBookings,
            'availability' => $availability,
            'nextBooking' => $upcomingBookings->first(),
        ]);
    }
}
