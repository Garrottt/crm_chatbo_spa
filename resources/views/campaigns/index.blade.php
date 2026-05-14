<x-app-layout>
    <div class="flex-1 overflow-y-auto bg-slate-100 px-6 py-6">
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-col gap-4 rounded-[28px] bg-slate-950 px-8 py-7 text-white shadow-xl lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.28em] text-indigo-300">Campañas</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight">Envios promocionales manuales</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Cree campañas, revise destinatarios, mida respuestas y empuje reservas al chatbot sin salir del CRM.</p>
                </div>
                <a href="{{ route('campaigns.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold uppercase tracking-[0.22em] text-slate-950 transition hover:bg-indigo-50">Nueva campaña</a>
            </div>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-5">
                    <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Seguimiento</p>
                    <h2 class="mt-1 text-xl font-black text-slate-900">Campañas creadas</h2>
                </div>

                @if($campaigns->isEmpty())
                    <div class="px-6 py-12 text-center text-sm font-medium text-slate-500">Todavia no hay campañas registradas.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                    <th class="px-6 py-4">Campaña</th>
                                    <th class="px-6 py-4">Oferta</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4">Aceptados</th>
                                    <th class="px-6 py-4">Respondieron</th>
                                    <th class="px-6 py-4">Reservaron</th>
                                    <th class="px-6 py-4">Conversion</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                                @foreach($campaigns as $campaign)
                                    @php
                                        $conversion = $campaign->sent_count > 0 ? round(($campaign->booked_count / $campaign->sent_count) * 100, 1) : 0;
                                        $campaignLabel = match ($campaign->status) {
                                            'DRAFT' => 'BORRADOR',
                                            'SENDING' => 'EN ENVIO',
                                            'SENT' => 'EN CURSO',
                                            'FINISHED' => 'FINALIZADA',
                                            'PAUSED' => 'PAUSADA',
                                            'CANCELLED' => 'CANCELADA',
                                            default => $campaign->status,
                                        };
                                        $campaignTone = match ($campaign->status) {
                                            'DRAFT' => 'bg-amber-50 text-amber-600',
                                            'SENDING', 'SENT' => 'bg-emerald-50 text-emerald-600',
                                            'PAUSED' => 'bg-indigo-50 text-indigo-600',
                                            'CANCELLED' => 'bg-rose-50 text-rose-600',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 align-top">
                                            <p class="font-bold text-slate-900">{{ $campaign->name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $campaign->objective }}</p>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <p>{{ $campaign->offer?->name ?: 'Sin oferta' }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $campaign->sentAt ? $campaign->sentAt->timezone('America/Santiago')->format('d/m/Y H:i') : 'Aun no enviada' }}</p>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.15em] {{ $campaignTone }}">{{ $campaignLabel }}</span>
                                        </td>
                                        <td class="px-6 py-4 align-top font-semibold text-slate-900">{{ $campaign->sent_count }}</td>
                                        <td class="px-6 py-4 align-top font-semibold text-slate-900">{{ $campaign->responded_count }}</td>
                                        <td class="px-6 py-4 align-top font-semibold text-slate-900">{{ $campaign->booked_count }}</td>
                                        <td class="px-6 py-4 align-top font-semibold text-slate-900">{{ $conversion }}%</td>
                                        <td class="px-6 py-4 align-top text-right">
                                            <div class="inline-flex gap-2">
                                                <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center rounded-2xl border border-slate-200 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-700 transition hover:bg-slate-50">Ver</a>
                                                @if($campaign->status === 'DRAFT')
                                                    <a href="{{ route('campaigns.edit', $campaign) }}" class="inline-flex items-center rounded-2xl border border-slate-200 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-700 transition hover:bg-slate-50">Editar</a>
                                                @endif
                                                <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}"
                                                      onsubmit="return confirm('¿Eliminar la campaña «{{ $campaign->name }}»? Esta acción no se puede deshacer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-rose-600 transition hover:bg-rose-100">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
