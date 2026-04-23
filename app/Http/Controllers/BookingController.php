<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookingController extends Controller
{
    public function getCalendarEvents(): JsonResponse
    {
        $query = Booking::with(['service', 'client', 'specialist']);

        $user = auth()->user();

        if ($user?->isSpecialist()) {
            $specialistId = $user->specialist?->id;

            if (!$specialistId) {
                return response()->json([]);
            }

            $query->where('specialistId', $specialistId);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => ($booking->client->name ?? 'Sin nombre') . ' - ' . ($booking->service->name ?? 'Servicio'),
                'start' => optional($booking->scheduledAt)->toIso8601String() ?? $booking->scheduledAt,
                'end' => optional($booking->endAt)->toIso8601String() ?? $booking->endAt,
                'color' => $this->statusColor($booking->status),
                'extendedProps' => [
                    'client' => $booking->client->name ?? 'Sin nombre',
                    'clientId' => $booking->clientId,
                    'service' => $booking->service->name ?? 'Sin servicio',
                    'specialist' => $booking->specialist->name ?? 'Sin asignar',
                    'status' => $booking->status,
                ],
            ];
        });

        return response()->json($events);
    }

    public function confirm(Booking $booking)
    {
        $this->ensureAdminCanManage($booking);

        $booking->update([
            'status' => 'CONFIRMED',
        ]);

        return response()->json([
            'message' => 'Reserva confirmada correctamente.',
            'booking' => $this->serializeBooking($booking->fresh(['client', 'service', 'specialist'])),
        ]);
    }

    public function cancel(Request $request, Booking $booking)
    {
        $this->ensureAdminCanManage($booking);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking->update([
            'status' => 'CANCELLED',
        ]);

        $booking->load(['client', 'service', 'specialist']);
        $this->sendCancellationNotification($booking, $data['reason'] ?? null);

        return response()->json([
            'message' => 'Reserva cancelada correctamente y cliente notificado.',
            'booking' => $this->serializeBooking($booking),
        ]);
    }

    public function reschedule(Request $request, Booking $booking)
    {
        $this->ensureAdminCanManage($booking);

        $data = $request->validate([
            'scheduledAt' => ['required', 'date'],
            'endAt' => ['required', 'date', 'after:scheduledAt'],
        ]);

        $newStatus = $booking->status === 'CANCELLED' ? 'PENDING' : $booking->status;

        $booking->update([
            'scheduledAt' => $data['scheduledAt'],
            'endAt' => $data['endAt'],
            'status' => $newStatus,
        ]);

        $booking->load(['client', 'service', 'specialist']);
        $this->sendRescheduleNotification($booking);

        return response()->json([
            'message' => 'Reserva reagendada correctamente y cliente notificado.',
            'booking' => $this->serializeBooking($booking),
        ]);
    }

    private function serializeBooking(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'title' => ($booking->client->name ?? 'Sin nombre') . ' - ' . ($booking->service->name ?? 'Servicio'),
            'start' => optional($booking->scheduledAt)->toIso8601String() ?? $booking->scheduledAt,
            'end' => optional($booking->endAt)->toIso8601String() ?? $booking->endAt,
            'status' => $booking->status,
            'client' => $booking->client->name ?? 'Sin nombre',
            'clientId' => $booking->clientId,
            'service' => $booking->service->name ?? 'Sin servicio',
            'specialist' => $booking->specialist->name ?? 'Sin asignar',
            'color' => $this->statusColor($booking->status),
        ];
    }

    private function sendRescheduleNotification(Booking $booking): void
    {
        $message = sprintf(
            'Hola %s, tu reserva para %s ha sido reagendada. Nueva fecha: %s. Hora de término: %s.',
            $booking->client->name ?? 'cliente',
            $booking->service->name ?? 'tu servicio',
            optional($booking->scheduledAt)->timezone(config('app.timezone'))->translatedFormat('d \\d\\e F \\a \\l\\a\\s H:i'),
            optional($booking->endAt)->timezone(config('app.timezone'))->translatedFormat('H:i')
        );

        $this->sendWhatsAppNotification($booking, $message);
    }

    private function sendCancellationNotification(Booking $booking, ?string $reason = null): void
    {
        $message = sprintf(
            'Hola %s, tu reserva para %s ha sido cancelada.',
            $booking->client->name ?? 'cliente',
            $booking->service->name ?? 'tu servicio'
        );

        if ($reason) {
            $message .= ' Motivo: ' . trim($reason) . '.';
        }

        $message .= ' Si necesitas ayuda para reagendar, escríbenos y con gusto te apoyamos.';

        $this->sendWhatsAppNotification($booking, $message);
    }

    private function sendWhatsAppNotification(Booking $booking, string $message): void
    {
        $client = $booking->client;

        if (!$client || empty($client->whatsappNumber)) {
            return;
        }

        $conversation = Conversation::where('clientId', $client->id)
            ->orderBy('updatedAt', 'desc')
            ->first();

        try {
            Http::post('http://localhost:3000/api/crm/send-message', [
                'whatsappNumber' => $client->whatsappNumber,
                'content' => $message,
                'conversationId' => $conversation?->id,
                'clientId' => $client->id,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function statusColor(?string $status): string
    {
        return match ($status) {
            'CONFIRMED' => '#10b981',
            'PENDING' => '#f59e0b',
            'CANCELLED' => '#ef4444',
            default => '#3b82f6',
        };
    }

    private function ensureAdminCanManage(Booking $booking): void
    {
        $user = auth()->user();

        abort_unless($user && $user->isAdmin(), 403);
    }
}
