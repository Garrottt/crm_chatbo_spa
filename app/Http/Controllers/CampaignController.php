<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertCampaignRequest;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Client;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();

        $campaigns = Campaign::query()
            ->with('offer')
            ->withCount([
                'recipients',
                'recipients as sent_count' => fn ($query) => $query->whereIn('status', ['SENT', 'RESPONDED', 'BOOKED', 'OPTED_OUT']),
                'recipients as responded_count' => fn ($query) => $query->whereIn('status', ['RESPONDED', 'BOOKED', 'OPTED_OUT']),
                'recipients as booked_count' => fn ($query) => $query->where('status', 'BOOKED'),
                'recipients as failed_count' => fn ($query) => $query->where('status', 'FAILED'),
            ])
            ->orderByDesc('createdAt')
            ->get();

        return view('campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $this->ensureAdmin();

        return view('campaigns.create', $this->formData(new Campaign([
            'objective' => 'Reactivacion',
            'segmentType' => 'inactive_30',
            'messageTemplate' => 'Hola {{nombre}}, hace un tiempo no te vemos por {{negocio}}. Tenemos {{beneficio}} en {{servicio}}. Responde "reservar" y te ayudo con una hora.',
        ])));
    }

    public function store(UpsertCampaignRequest $request): RedirectResponse
    {
        $this->ensureAdmin();

        $campaign = Campaign::create([
            'id' => (string) Str::uuid(),
            ...$this->payload($request),
            'status' => 'DRAFT',
            'createdByUserId' => (string) auth()->id(),
        ]);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campana guardada en borrador. Revise la previsualizacion antes de enviar.');
    }

    public function edit(Campaign $campaign): View
    {
        $this->ensureAdmin();

        abort_if($campaign->status !== 'DRAFT', 422, 'Solo puede editar campanas en borrador.');

        return view('campaigns.edit', $this->formData($campaign));
    }

    public function update(UpsertCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $this->ensureAdmin();

        abort_if($campaign->status !== 'DRAFT', 422, 'Solo puede editar campanas en borrador.');

        $campaign->update($this->payload($request));

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campana actualizada correctamente.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->ensureAdmin();

        $name = $campaign->name;

        // Elimina destinatarios primero (por si no hay cascade en la BD)
        $campaign->recipients()->delete();
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', "Campana «{$name}» eliminada correctamente.");
    }

    public function show(Campaign $campaign): View
    {
        $this->ensureAdmin();

        $campaign->load([
            'offer.service',
            'offer.specialist',
            'recipients.client',
            'recipients.booking',
        ]);

        $previewRecipients = $campaign->status === 'DRAFT'
            ? $this->resolveRecipients($campaign)->take(100)
            : collect();

        $metrics = [
            'total' => $campaign->recipients->count(),
            'sent' => $campaign->recipients->whereIn('status', ['SENT', 'RESPONDED', 'BOOKED', 'OPTED_OUT'])->count(),
            'responded' => $campaign->recipients->whereIn('status', ['RESPONDED', 'BOOKED', 'OPTED_OUT'])->count(),
            'booked' => $campaign->recipients->where('status', 'BOOKED')->count(),
            'failed' => $campaign->recipients->where('status', 'FAILED')->count(),
        ];
        $metrics['conversion'] = $metrics['sent'] > 0 ? round(($metrics['booked'] / $metrics['sent']) * 100, 1) : 0;

        return view('campaigns.show', compact('campaign', 'previewRecipients', 'metrics'));
    }

    public function send(Campaign $campaign): RedirectResponse
    {
        $this->ensureAdmin();

        abort_if($campaign->status !== 'DRAFT', 422, 'Solo puede enviar campanas en borrador.');

        $campaign->load('offer.service', 'offer.specialist');
        $this->assertOfferCanBeSent($campaign->offer);

        $recipients = $this->resolveRecipients($campaign);
        if ($recipients->isEmpty()) {
            return back()->with('error', 'No hay destinatarios validos para esta campana.');
        }

        $campaign->update([
            'status' => 'SENDING',
        ]);

        foreach ($recipients as $client) {
            $message = $this->renderMessageTemplate($campaign, $client);
            $recipient = CampaignRecipient::firstOrCreate(
                [
                    'campaignId' => $campaign->id,
                    'clientId' => $client->id,
                ],
                [
                    'id' => (string) Str::uuid(),
                    'status' => 'PENDING',
                    'messageSnapshot' => $message,
                ]
            );

            try {
                $response = Http::connectTimeout(15)->timeout(120)->post($this->chatbotEndpoint('/api/crm/send-campaign'), [
                    'whatsappNumber'     => $client->whatsappNumber,
                    'clientId'           => $client->id,
                    'campaignId'         => $campaign->id,
                    'offerId'            => $campaign->offerId,
                    'campaignRecipientId'=> $recipient->id,
                    'message'            => $message,
                    'serviceId'          => $campaign->offer?->serviceId,
                    'specialistId'       => $campaign->offer?->specialistId,
                    'templateName'       => config('services.chatbot.campaign_template_name') ?: null,
                    'templateLanguage'   => config('services.chatbot.campaign_template_language', 'es'),
                ]);

                if (! $response->successful()) {
                    $recipient->update([
                        'status' => 'FAILED',
                        'failedReason' => trim('HTTP ' . $response->status() . ' ' . (string) $response->body()) ?: 'El chatbot no acepto el envio.',
                    ]);
                }
            } catch (\Throwable $exception) {
                $recipient->update([
                    'status' => 'FAILED',
                    'failedReason' => $exception->getMessage(),
                ]);
            }
        }

        $campaign->update([
            'status' => 'SENT',
            'sentAt' => now(),
        ]);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campana enviada. Revise abajo el estado de cada destinatario.');
    }

    private function formData(Campaign $campaign): array
    {
        return [
            'campaign' => $campaign,
            'offers' => Offer::query()->where('active', true)->with('service')->orderBy('name')->get(),

        ];
    }

    private function payload(UpsertCampaignRequest $request): array
    {
        $data = $request->validated();

        return [
            'name' => $data['name'],
            'objective' => $data['objective'],
            'offerId' => $data['offerId'],
            'segmentType' => $data['segmentType'],
            'messageTemplate' => $data['messageTemplate'],
            'segmentFilter' => [
                'minBookings' => (int) ($data['minBookings'] ?? 5),
                'lookbackDays' => (int) ($data['lookbackDays'] ?? 30),
                'ignoreCooldown' => (bool) $request->boolean('ignoreCooldown', false),
            ],
        ];
    }

    private function resolveRecipients(Campaign $campaign): Collection
    {
        $campaign->loadMissing('offer');
        $filters = $campaign->segmentFilter ?? [];
        $lookbackDays = max(1, (int) ($filters['lookbackDays'] ?? 30));
        $minBookings = max(1, (int) ($filters['minBookings'] ?? 5));
        $threshold = now()->subDays($campaign->segmentType === 'inactive_90' ? 90 : $lookbackDays);
        $ignoreCooldown = (bool) ($filters['ignoreCooldown'] ?? false);

        $query = Client::query()
            ->where('marketingOptOut', false)
            ->whereNotNull('whatsappNumber')
            ->where('whatsappNumber', '!=', '')
            ->whereDoesntHave('campaignRecipients', fn (Builder $sub) => $sub
                ->where('campaignId', $campaign->id)
                ->whereNotIn('status', ['FAILED'])
            );

        if (! $ignoreCooldown) {
            $cooldownThreshold = now()->subDays(30);
            $query->whereDoesntHave('campaignRecipients', function (Builder $sub) use ($cooldownThreshold) {
                $sub->whereIn('status', ['SENT', 'RESPONDED', 'BOOKED', 'OPTED_OUT', 'SKIPPED'])
                    ->whereHas('campaign', function (Builder $campaignQuery) use ($cooldownThreshold) {
                        $campaignQuery->where('sentAt', '>=', $cooldownThreshold);
                    });
            });
        }

        if ($campaign->offer?->serviceId) {
            $query->whereDoesntHave('bookings', function (Builder $sub) use ($campaign) {
                $sub->where('serviceId', $campaign->offer->serviceId)
                    ->whereIn('status', ['PENDING', 'CONFIRMED'])
                    ->where('scheduledAt', '>=', now());
            });
        }

        if ($campaign->segmentType === 'inactive_30' || $campaign->segmentType === 'inactive_90') {
            $query->whereHas('bookings', fn (Builder $sub) => $sub->where('status', 'CONFIRMED'))
                ->whereDoesntHave('bookings', function (Builder $sub) use ($threshold) {
                    $sub->where('status', 'CONFIRMED')
                        ->where('scheduledAt', '>=', $threshold);
                });
        }

        if ($campaign->segmentType === 'frequent') {
            $query->whereHas('bookings', function (Builder $sub) {
                $sub->where('status', 'CONFIRMED');
            }, '>=', $minBookings);
        }

        if ($campaign->segmentType === 'consulted_no_booking') {
            $query->where(function (Builder $sub) use ($threshold) {
                $sub->whereHas('conversations', fn (Builder $conversationQuery) => $conversationQuery->where('updatedAt', '>=', $threshold))
                    ->orWhereHas('messages', fn (Builder $messageQuery) => $messageQuery->where('createdAt', '>=', $threshold));
            })->whereDoesntHave('bookings', function (Builder $sub) use ($threshold) {
                $sub->where('status', 'CONFIRMED')
                    ->where('scheduledAt', '>=', $threshold);
            });
        }

        return $query->orderBy('name')->get();
    }

    private function renderMessageTemplate(Campaign $campaign, Client $client): string
    {
        $offer = $campaign->offer;
        $serviceName = $offer?->service?->name ?: 'nuestros servicios';
        $benefit = $this->formatBenefit($offer);

        return strtr($campaign->messageTemplate, [
            '{{nombre}}' => trim((string) ($client->name ?: 'cliente')),
            '{{negocio}}' => config('app.name', 'Spa Ikigai Ovalle'),
            '{{oferta}}' => $offer?->name ?: 'esta promocion',
            '{{beneficio}}' => $benefit,
            '{{servicio}}' => $serviceName,
        ]);
    }

    private function formatBenefit(?Offer $offer): string
    {
        if (! $offer) {
            return 'una promocion especial';
        }

        if ($offer->discountType === 'PERCENTAGE') {
            return $offer->discountValue . '% de descuento';
        }

        if ($offer->discountType === 'FIXED_AMOUNT') {
            return '$' . number_format((int) $offer->discountValue, 0, ',', '.') . ' CLP de descuento';
        }

        return $offer->customText ?: $offer->description;
    }

    private function assertOfferCanBeSent(?Offer $offer): void
    {
        abort_if(! $offer, 422, 'La campana no tiene una oferta valida asociada.');
        abort_if(! $offer->active, 422, 'La oferta asociada esta inactiva.');

        $now = now();
        abort_if($offer->startsAt && $offer->startsAt->isFuture(), 422, 'La oferta asociada aun no inicia su vigencia.');
        abort_if($offer->endsAt && $offer->endsAt->lt($now), 422, 'La oferta asociada ya vencio.');
    }

    private function chatbotEndpoint(string $path): string
    {
        return rtrim((string) config('services.chatbot.base_url'), '/') . $path;
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}



