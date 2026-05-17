<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(Request $request): View
    {
        $this->syncRecentOperationalAlerts();

        $alerts = $this->visibleAlertsQuery()
            ->latest('createdAt')
            ->paginate(30);

        return view('alerts.index', compact('alerts'));
    }

    public function summary(): JsonResponse
    {
        $this->syncRecentOperationalAlerts();

        $query = $this->visibleAlertsQuery();

        $unreadCount = (clone $query)
            ->whereNull('readAt')
            ->count();

        $alerts = (clone $query)
            ->latest('createdAt')
            ->limit(8)
            ->get()
            ->map(fn (Alert $alert) => $this->serializeAlert($alert))
            ->values();

        return response()->json([
            'unreadCount' => $unreadCount,
            'alerts' => $alerts,
        ]);
    }

    public function read(Alert $alert): JsonResponse|RedirectResponse
    {
        abort_unless($this->canSeeAlert($alert), 403);

        if (!$alert->readAt) {
            $alert->update(['readAt' => now()]);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'ok' => true,
                'alert' => $this->serializeAlert($alert->fresh()),
            ]);
        }

        return back();
    }

    public function readAll(): JsonResponse|RedirectResponse
    {
        $this->visibleAlertsQuery()
            ->whereNull('readAt')
            ->update(['readAt' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    private function visibleAlertsQuery()
    {
        $user = auth()->user();

        abort_unless($user, 403);

        return Alert::query()
            ->where(function ($query) use ($user) {
                $query->where('recipientUserId', $user->id);

                if ($user->isAdmin()) {
                    $query->orWhere(function ($roleQuery) {
                        $roleQuery->whereNull('recipientUserId')
                            ->where('recipientRole', 'ADMIN');
                    });
                }

                if ($user->isSpecialist()) {
                    $query->orWhere(function ($roleQuery) {
                        $roleQuery->whereNull('recipientUserId')
                            ->where('recipientRole', 'SPECIALIST');
                    });
                }
            });
    }

    private function syncRecentOperationalAlerts(): void
    {
        Booking::with(['client', 'service', 'specialist.user'])
            ->whereIn('status', ['CONFIRMED', 'CANCELLED'])
            ->where('updatedAt', '>=', Carbon::now()->subDays(2))
            ->latest('updatedAt')
            ->limit(50)
            ->get()
            ->each(fn (Booking $booking) => $this->createMissingBookingAlerts($booking));
    }

    private function createMissingBookingAlerts(Booking $booking): void
    {
        $status = strtoupper((string) $booking->status);
        $isCancelled = $status === 'CANCELLED';
        $eventStatus = $isCancelled ? 'cancelled' : 'confirmed';
        $type = $isCancelled ? 'booking_cancelled' : 'booking_confirmed';
        $title = $isCancelled ? 'Reserva cancelada' : 'Nueva reserva confirmada';
        $clientName = $booking->client->name ?? 'Cliente';
        $serviceName = $booking->service->name ?? 'Servicio';
        $specialistName = $booking->specialist?->name ?? 'Sin especialista asignado';
        $dateLabel = sprintf(
            '%s - %s',
            optional($booking->scheduledAt)->copy()->timezone('America/Santiago')->format('d/m/Y H:i'),
            optional($booking->endAt)->copy()->timezone('America/Santiago')->format('H:i')
        );
        $body = $isCancelled
            ? "{$clientName} cancelo {$serviceName} para {$dateLabel}. Especialista: {$specialistName}."
            : "{$clientName} confirmo {$serviceName} para {$dateLabel}. Especialista: {$specialistName}.";

        $recipients = User::query()
            ->where('role', 'ADMIN')
            ->get(['id', 'role'])
            ->map(fn (User $user) => ['id' => $user->id, 'role' => $user->role])
            ->values();

        if ($booking->specialist?->userId) {
            $recipients->push(['id' => $booking->specialist->userId, 'role' => 'SPECIALIST']);
        }

        foreach ($recipients->unique('id') as $recipient) {
            Alert::query()->firstOrCreate(
                ['eventKey' => "booking:{$booking->id}:{$eventStatus}:user:{$recipient['id']}"],
                [
                    'type' => $type,
                    'title' => $title,
                    'body' => $body,
                    'recipientUserId' => $recipient['id'],
                    'recipientRole' => $recipient['role'],
                    'clientId' => $booking->clientId,
                    'bookingId' => $booking->id,
                    'conversationId' => null,
                    'actionUrl' => "/agenda?booking={$booking->id}",
                    'metadata' => [
                        'source' => 'crm_sync',
                        'serviceName' => $serviceName,
                        'specialistName' => $specialistName,
                    ],
                ]
            );
        }
    }

    private function canSeeAlert(Alert $alert): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ((string) $alert->recipientUserId === (string) $user->id) {
            return true;
        }

        return !$alert->recipientUserId
            && (($user->isAdmin() && $alert->recipientRole === 'ADMIN')
                || ($user->isSpecialist() && $alert->recipientRole === 'SPECIALIST'));
    }

    private function serializeAlert(Alert $alert): array
    {
        return [
            'id' => $alert->id,
            'type' => $alert->type,
            'title' => $alert->title,
            'body' => $alert->body,
            'actionUrl' => $alert->actionUrl,
            'read' => !$alert->isUnread(),
            'createdAt' => optional($alert->createdAt)->timezone('America/Santiago')->format('d/m H:i'),
        ];
    }
}
