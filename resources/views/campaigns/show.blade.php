<x-app-layout>
    <div class="flex-1 overflow-y-auto bg-slate-100 px-6 py-6">
        <div class="mx-auto max-w-7xl space-y-6">
            @php
                $campaignLabel = match ($campaign->status) {
                    'DRAFT' => 'BORRADOR',
                    'SENDING' => 'EN ENVIO',
                    'SENT' => 'EN CURSO',
                    'FINISHED' => 'FINALIZADA',
                    'PAUSED' => 'PAUSADA',
                    'CANCELLED' => 'CANCELADA',
                    default => $campaign->status,
                };
            @endphp

            <div class="flex flex-col gap-4 rounded-[28px] bg-slate-950 px-8 py-7 text-white shadow-xl lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.28em] text-indigo-300">Campañas</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight">{{ $campaign->name }}</h1>
                    <p class="mt-2 max-w-3xl text-sm text-slate-300">{{ $campaign->objective }}. Oferta asociada: {{ $campaign->offer?->name ?: 'Sin oferta' }}.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if($campaign->status === 'DRAFT')
                        <a href="{{ route('campaigns.edit', $campaign) }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-white/5">Editar</a>
                        <form method="POST" action="{{ route('campaigns.send', $campaign) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-indigo-50">Enviar campaña</button>
                        </form>
                    @endif
                    <a href="{{ route('campaigns.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-white/5">Volver</a>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700">{{ session('error') }}</div>
            @endif

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm"><p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Estado</p><p class="mt-3 text-2xl font-black text-slate-900">{{ $campaignLabel }}</p><p class="mt-1 text-sm text-slate-500">{{ $campaign->sentAt ? $campaign->sentAt->timezone('America/Santiago')->format('d/m/Y H:i') : 'Sin envio aun' }}</p></div>
                <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm"><p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Destinatarios</p><p class="mt-3 text-4xl font-black text-slate-900">{{ $metrics['total'] }}</p><p class="mt-1 text-sm text-slate-500">Registrados en la campana</p></div>
                <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm"><p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Aceptados</p><p class="mt-3 text-4xl font-black text-slate-900">{{ $metrics['sent'] }}</p><p class="mt-1 text-sm text-slate-500">Mensajes aceptados por Meta</p></div>
                <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm"><p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Respondieron</p><p class="mt-3 text-4xl font-black text-slate-900">{{ $metrics['responded'] }}</p><p class="mt-1 text-sm text-slate-500">Clientes con reaccion</p></div>
                <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm"><p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Reservaron</p><p class="mt-3 text-4xl font-black text-slate-900">{{ $metrics['booked'] }}</p><p class="mt-1 text-sm text-slate-500">Conversion {{ $metrics['conversion'] }}%</p></div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.05fr,0.95fr]">
                <div class="space-y-6">
                    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Oferta vinculada</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-900">{{ $campaign->offer?->name ?: 'Sin oferta' }}</h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $campaign->offer?->description }}</p>
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-600"><span class="block text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Servicio</span><span class="mt-2 block font-semibold text-slate-900">{{ $campaign->offer?->service?->name ?: 'Sin servicio fijo' }}</span></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-600"><span class="block text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Beneficio</span><span class="mt-2 block font-semibold text-slate-900">@if($campaign->offer?->discountType === 'PERCENTAGE') {{ $campaign->offer->discountValue }}% de descuento @elseif($campaign->offer?->discountType === 'FIXED_AMOUNT') ${{ number_format((int) $campaign->offer->discountValue, 0, ',', '.') }} CLP @else {{ $campaign->offer?->customText ?: 'Texto comercial' }} @endif</span></div>
                        </div>
                    </section>

                    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Mensaje</p>
                        <div class="mt-4 rounded-2xl bg-slate-50 px-5 py-5 text-sm leading-7 text-slate-700 whitespace-pre-wrap">{{ $campaign->messageTemplate }}</div>
                    </section>

                    @if($campaign->status === 'DRAFT')
                        <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Previsualizacion</p>
                                    <h2 class="mt-1 text-xl font-black text-slate-900">Destinatarios calculados</h2>
                                </div>
                                <span class="inline-flex rounded-full bg-indigo-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">{{ $previewRecipients->count() }} mostrados</span>
                            </div>
                            <div class="mt-5 space-y-3">
                                @forelse($previewRecipients as $client)
                                    <div class="rounded-2xl border border-slate-200 px-4 py-4 text-sm text-slate-700">
                                        <p class="font-bold text-slate-900">{{ trim(($client->name ?? '') . ' ' . ($client->lastName ?? '')) ?: 'Cliente sin nombre' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $client->whatsappNumber }} @if($client->lastBookingAt) - Ultima reserva {{ $client->lastBookingAt->timezone('America/Santiago')->format('d/m/Y') }} @endif</p>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm font-medium text-slate-500">No hay destinatarios elegibles para esta campana con los filtros actuales.</div>
                                @endforelse
                            </div>
                        </section>
                    @endif
                </div>

                <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Resultados</p>
                            <h2 class="mt-1 text-xl font-black text-slate-900">Estado por destinatario</h2>
                        </div>
                    </div>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                    <th class="px-4 py-3">Cliente</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3">Aceptado</th>
                                    <th class="px-4 py-3">Entrega</th>
                                    <th class="px-4 py-3">Respondio</th>
                                    <th class="px-4 py-3">Reserva</th>
                                    <th class="px-4 py-3">Detalle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                                @forelse($campaign->recipients->sortByDesc('createdAt') as $recipient)
                                    @php
                                        $deliveryLabel = match (true) {
                                            $recipient->status === 'BOOKED' => 'RESERVO',
                                            $recipient->status === 'OPTED_OUT' => 'BAJA',
                                            $recipient->status === 'RESPONDED' => 'RESPONDIO',
                                            $recipient->status === 'FAILED' => 'FALLIDO',
                                            $recipient->deliveryStatus === 'read' => 'LEIDO',
                                            $recipient->deliveryStatus === 'delivered' => 'ENTREGADO',
                                            in_array($recipient->deliveryStatus, ['accepted', 'sent']) || $recipient->status === 'SENT' => 'ACEPTADO',
                                            default => 'PENDIENTE',
                                        };
                                        $deliveryTone = match ($deliveryLabel) {
                                            'RESERVO' => 'bg-emerald-50 text-emerald-700',
                                            'RESPONDIO' => 'bg-sky-50 text-sky-700',
                                            'LEIDO' => 'bg-indigo-50 text-indigo-700',
                                            'ENTREGADO' => 'bg-violet-50 text-violet-700',
                                            'ACEPTADO' => 'bg-amber-50 text-amber-700',
                                            'FALLIDO' => 'bg-rose-50 text-rose-700',
                                            'BAJA' => 'bg-slate-100 text-slate-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                        $deliveryTime = $recipient->readAt ?: $recipient->deliveredAt ?: $recipient->failedAt;
                                        $detailText = $recipient->deliveryError ?: $recipient->failedReason ?: \Illuminate\Support\Str::limit($recipient->messageSnapshot, 80);
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 align-top">
                                            <p class="font-semibold text-slate-900">{{ trim(($recipient->client?->name ?? '') . ' ' . ($recipient->client?->lastName ?? '')) ?: 'Cliente sin nombre' }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $recipient->client?->whatsappNumber }}</p>
                                        </td>
                                        <td class="px-4 py-3 align-top"><span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] {{ $deliveryTone }}">{{ $deliveryLabel }}</span></td>
                                        <td class="px-4 py-3 align-top text-xs text-slate-500">{{ $recipient->sentAt ? $recipient->sentAt->timezone('America/Santiago')->format('d/m H:i') : 'Pendiente' }}</td>
                                        <td class="px-4 py-3 align-top text-xs text-slate-500">{{ $deliveryTime ? $deliveryTime->timezone('America/Santiago')->format('d/m H:i') : '-' }}</td>
                                        <td class="px-4 py-3 align-top text-xs text-slate-500">{{ $recipient->respondedAt ? $recipient->respondedAt->timezone('America/Santiago')->format('d/m H:i') : '-' }}</td>
                                        <td class="px-4 py-3 align-top text-xs text-slate-500">{{ $recipient->bookedAt ? $recipient->bookedAt->timezone('America/Santiago')->format('d/m H:i') : '-' }}</td>
                                        <td class="px-4 py-3 align-top text-xs text-slate-500">{{ $detailText }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-8 text-center text-sm font-medium text-slate-500">Esta campaña aun no tiene destinatarios registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
