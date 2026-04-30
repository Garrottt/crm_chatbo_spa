<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Specialist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class BookingController extends Controller
{
    private const SPA_TIMEZONE = 'America/Santiago';

    public function getCalendarEvents(Request $request): JsonResponse
    {
        $query = Booking::with(['service', 'client', 'specialist']);

        $start = $request->date('start');
        $end = $request->date('end');

        if ($start && $end) {
            $query->where(function ($bookingQuery) use ($start, $end) {
                $bookingQuery
                    ->whereBetween('scheduledAt', [$start, $end])
                    ->orWhereBetween('endAt', [$start, $end])
                    ->orWhere(function ($overlapQuery) use ($start, $end) {
                        $overlapQuery
                            ->where('scheduledAt', '<=', $start)
                            ->where('endAt', '>=', $end);
                    });
            });
        }

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
                'start' => $this->toSpaIsoString($booking->scheduledAt),
                'end' => $this->toSpaIsoString($booking->endAt),
                'color' => $this->statusColor($booking->status),
                'extendedProps' => [
                    'client' => $booking->client->name ?? 'Sin nombre',
                    'clientId' => $booking->clientId,
                    'service' => $booking->service->name ?? 'Sin servicio',
                    'serviceDurationMinutes' => $booking->service->durationMinutes ?? null,
                    'specialistId' => $booking->specialistId,
                    'specialist' => $booking->specialist?->name ?? 'Sin asignar',
                    'status' => $booking->status,
                ],
            ];
        });

        return response()->json($events);
    }

    public function specialistsOptions(): JsonResponse
    {
        if (!Schema::hasTable('Specialist')) {
            return response()->json([]);
        }

        $specialists = Specialist::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($specialists->map(fn (Specialist $specialist) => [
            'id' => $specialist->id,
            'name' => $specialist->name,
        ])->values());
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
        $this->dispatchNotificationAfterResponse($booking->id, 'cancellation', $data['reason'] ?? null);

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
        ]);

        $newStatus = $booking->status === 'CANCELLED' ? 'PENDING' : $booking->status;
        $durationMinutes = max(1, (int) ($booking->service->durationMinutes ?? 60));
        $newStart = Carbon::createFromFormat('Y-m-d\TH:i', $data['scheduledAt'], self::SPA_TIMEZONE)->utc();
        $newEnd = (clone $newStart)->addMinutes($durationMinutes);

        $booking->update([
            'scheduledAt' => $newStart,
            'endAt' => $newEnd,
            'status' => $newStatus,
        ]);

        $booking->load(['client', 'service', 'specialist']);
        $this->dispatchNotificationAfterResponse($booking->id, 'reschedule');

        return response()->json([
            'message' => 'Reserva reagendada correctamente y cliente notificado.',
            'booking' => $this->serializeBooking($booking),
        ]);
    }

    public function assignSpecialist(Request $request, Booking $booking)
    {
        $this->ensureAdminCanManage($booking);

        $rules = [
            'specialistId' => ['nullable', 'string'],
        ];

        if (Schema::hasTable('Specialist')) {
            $rules['specialistId'][] = 'exists:Specialist,id';
        }

        $data = $request->validate($rules);

        $booking->update([
            'specialistId' => $data['specialistId'] ?? null,
        ]);

        $booking->load(['client', 'service', 'specialist']);

        return response()->json([
            'message' => $booking->specialist
                ? 'Especialista asignado correctamente.'
                : 'Reserva marcada sin especialista asignado.',
            'booking' => $this->serializeBooking($booking),
        ]);
    }

    private function serializeBooking(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'title' => ($booking->client->name ?? 'Sin nombre') . ' - ' . ($booking->service->name ?? 'Servicio'),
            'start' => $this->toSpaIsoString($booking->scheduledAt),
            'end' => $this->toSpaIsoString($booking->endAt),
            'status' => $booking->status,
            'client' => $booking->client->name ?? 'Sin nombre',
            'clientId' => $booking->clientId,
            'service' => $booking->service->name ?? 'Sin servicio',
            'serviceDurationMinutes' => $booking->service->durationMinutes ?? null,
            'specialistId' => $booking->specialistId,
            'specialist' => $booking->specialist?->name ?? 'Sin asignar',
            'color' => $this->statusColor($booking->status),
        ];
    }

    private function sendRescheduleNotification(Booking $booking): void
    {
        $message = sprintf(
            'Hola %s, tu reserva para %s ha sido reagendada. Nueva fecha: %s. Hora de termino: %s.',
            $booking->client->name ?? 'cliente',
            $booking->service->name ?? 'tu servicio',
            optional($booking->scheduledAt)->copy()->timezone(self::SPA_TIMEZONE)->locale('es')->translatedFormat('d \\d\\e F \\a \\l\\a\\s H:i'),
            optional($booking->endAt)->copy()->timezone(self::SPA_TIMEZONE)->locale('es')->translatedFormat('H:i')
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

        $message .= ' Si necesitas ayuda para reagendar, escribenos y con gusto te apoyamos.';

        $this->sendWhatsAppNotification($booking, $message);
    }

    private function toSpaIsoString($value): ?string
    {
        if (!$value) {
            return null;
        }

        return $value->copy()->timezone(self::SPA_TIMEZONE)->toIso8601String();
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
            Http::connectTimeout(2)->timeout(5)->post($this->chatbotEndpoint('/api/crm/send-message'), [
                'whatsappNumber' => $client->whatsappNumber,
                'content' => $message,
                'conversationId' => $conversation?->id,
                'clientId' => $client->id,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function dispatchNotificationAfterResponse(string $bookingId, string $type, ?string $reason = null): void
    {
        app()->terminating(function () use ($bookingId, $type, $reason) {
            $booking = Booking::with(['client', 'service', 'specialist'])->find($bookingId);

            if (!$booking) {
                return;
            }

            if ($type === 'cancellation') {
                $this->sendCancellationNotification($booking, $reason);
                return;
            }

            if ($type === 'reschedule') {
                $this->sendRescheduleNotification($booking);
            }
        });
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

    private function chatbotEndpoint(string $path): string
    {
        return rtrim((string) config('services.chatbot.base_url'), '/') . $path;
    }
}

