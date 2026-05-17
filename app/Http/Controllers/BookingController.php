<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Alert;
use App\Models\Specialist;
use App\Models\User;
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
        $this->markCompletedBookings();

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
        abort_unless(auth()->user()?->isAdmin(), 403);

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

        $booking->load(['client', 'service', 'specialist']);
        $this->createBookingAlert($booking, 'confirmed', 'crm');
        $this->dispatchNotificationAfterResponse($booking->id, 'confirmation');

        return response()->json([
            'message' => 'Reserva confirmada correctamente.',
            'booking' => $this->serializeBooking($booking),
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
        $this->createBookingAlert($booking, 'cancelled', 'crm', $data['reason'] ?? null);
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

        $newStatus = in_array($booking->status, ['CANCELLED', 'COMPLETED'], true) ? 'PENDING' : $booking->status;
        $durationMinutes = max(1, (int) ($booking->service->durationMinutes ?? 60));
        $newStart = Carbon::createFromFormat('Y-m-d\TH:i', $data['scheduledAt'], self::SPA_TIMEZONE)->utc();
        $newEnd = (clone $newStart)->addMinutes($durationMinutes);
        $scheduleValidationMessage = $this->validateBookingInsideBusinessHours($newStart, $newEnd);

        if ($scheduleValidationMessage) {
            return response()->json([
                'message' => $scheduleValidationMessage,
            ], 422);
        }

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

    public function destroy(Booking $booking)
    {
        if ($booking->status !== 'CANCELLED') {
            return response()->json([
                'message' => 'Solo es posible eliminar del calendario reservas que ya esten canceladas.',
            ], 422);
        }

        $user = auth()->user();
        $canDelete = $user && (
            $user->isAdmin()
            || ($user->isSpecialist() && $user->specialist?->id && $user->specialist->id === $booking->specialistId)
        );

        abort_unless($canDelete, 403);

        $bookingId = $booking->id;
        $booking->delete();

        return response()->json([
            'message' => 'Reserva cancelada eliminada del calendario correctamente.',
            'bookingId' => $bookingId,
        ]);
    }

    public function assignSpecialist(Request $request, Booking $booking)
    {
        $this->ensureAdminCanManage($booking);

        if (in_array($booking->status, ['CANCELLED', 'COMPLETED'], true)) {
            return response()->json([
                'message' => 'No es posible cambiar el especialista de una reserva cancelada o completada.',
            ], 422);
        }

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
        $this->dispatchNotificationAfterResponse($booking->id, 'specialist_assignment');

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

    private function sendConfirmationNotification(Booking $booking): void
    {
        $message = sprintf(
            'Hola %s, tu reserva para %s ha sido confirmada. Fecha: %s. Hora de termino: %s. Especialista asignado: %s.',
            $booking->client->name ?? 'cliente',
            $booking->service->name ?? 'tu servicio',
            optional($booking->scheduledAt)->copy()->timezone(self::SPA_TIMEZONE)->locale('es')->translatedFormat('d \\d\\e F \\a \\l\\a\\s H:i'),
            optional($booking->endAt)->copy()->timezone(self::SPA_TIMEZONE)->locale('es')->translatedFormat('H:i'),
            $booking->specialist?->name ?? 'Sin especialista asignado'
        );

        $this->sendWhatsAppNotification($booking, $message);
    }

    private function sendSpecialistAssignmentNotification(Booking $booking): void
    {
        $message = sprintf(
            'Hola %s, tu reserva para %s fue actualizada. Fecha: %s. Hora de termino: %s. Especialista asignado: %s.',
            $booking->client->name ?? 'cliente',
            $booking->service->name ?? 'tu servicio',
            optional($booking->scheduledAt)->copy()->timezone(self::SPA_TIMEZONE)->locale('es')->translatedFormat('d \\d\\e F \\a \\l\\a\\s H:i'),
            optional($booking->endAt)->copy()->timezone(self::SPA_TIMEZONE)->locale('es')->translatedFormat('H:i'),
            $booking->specialist?->name ?? 'Sin especialista asignado'
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

        $message .= ' El equipo del spa se pondra en contacto contigo para acordar la devolucion de tu abono. Si necesitas ayuda para reagendar, escribenos y con gusto te apoyamos.';

        $this->sendWhatsAppNotification($booking, $message);
    }

    private function toSpaIsoString($value): ?string
    {
        if (!$value) {
            return null;
        }

        return $value->copy()->timezone(self::SPA_TIMEZONE)->toIso8601String();
    }

    private function validateBookingInsideBusinessHours(Carbon $startUtc, Carbon $endUtc): ?string
    {
        $start = $startUtc->copy()->timezone(self::SPA_TIMEZONE);
        $end = $endUtc->copy()->timezone(self::SPA_TIMEZONE);
        $dayOfWeek = $start->dayOfWeek;

        if ($dayOfWeek === Carbon::SUNDAY) {
            return 'No es posible reagendar reservas para el domingo porque el spa no atiende ese dia.';
        }

        $closingHour = $dayOfWeek === Carbon::SATURDAY ? 18 : 19;
        $closingTime = $start->copy()->setTime($closingHour, 0, 0);

        if ($end->greaterThan($closingTime)) {
            $closingLabel = $dayOfWeek === Carbon::SATURDAY
                ? '18:00 los sabados'
                : '19:00 de lunes a viernes';

            return "No es posible reagendar esta reserva porque superaria el horario de atencion del spa. El horario maximo permitido es hasta las {$closingLabel}.";
        }

        return null;
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

            if ($type === 'confirmation') {
                $this->sendConfirmationNotification($booking);
                return;
            }

            if ($type === 'specialist_assignment') {
                $this->sendSpecialistAssignmentNotification($booking);
                return;
            }

            if ($type === 'reschedule') {
                $this->sendRescheduleNotification($booking);
            }
        });
    }

    private function createBookingAlert(Booking $booking, string $status, string $source, ?string $reason = null): void
    {
        $booking->loadMissing(['client', 'service', 'specialist.user']);

        $isCancelled = $status === 'cancelled';
        $type = $isCancelled ? 'booking_cancelled' : 'booking_confirmed';
        $title = $isCancelled ? 'Reserva cancelada' : 'Nueva reserva confirmada';
        $clientName = $booking->client->name ?? 'Cliente';
        $serviceName = $booking->service->name ?? 'Servicio';
        $specialistName = $booking->specialist?->name ?? 'Sin especialista asignado';
        $dateLabel = sprintf(
            '%s - %s',
            optional($booking->scheduledAt)->copy()->timezone(self::SPA_TIMEZONE)->format('d/m/Y H:i'),
            optional($booking->endAt)->copy()->timezone(self::SPA_TIMEZONE)->format('H:i')
        );

        $body = $isCancelled
            ? "{$clientName} cancelo {$serviceName} para {$dateLabel}. Especialista: {$specialistName}."
            : "{$clientName} confirmo {$serviceName} para {$dateLabel}. Especialista: {$specialistName}.";

        if ($reason) {
            $body .= ' Motivo: ' . trim($reason) . '.';
        }

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
                ['eventKey' => "booking:{$booking->id}:{$status}:user:{$recipient['id']}"],
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
                        'source' => $source,
                        'serviceName' => $serviceName,
                        'specialistName' => $specialistName,
                        'reason' => $reason,
                    ],
                ]
            );
        }
    }

    private function markCompletedBookings(): void
    {
        Booking::query()
            ->where('status', 'CONFIRMED')
            ->whereNotNull('endAt')
            ->where('endAt', '<=', Carbon::now(self::SPA_TIMEZONE)->subMinute()->utc())
            ->update([
                'status' => 'COMPLETED',
            ]);
    }

    private function statusColor(?string $status): string
    {
        return match ($status) {
            'CONFIRMED' => '#10b981',
            'COMPLETED' => '#64748b',
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


