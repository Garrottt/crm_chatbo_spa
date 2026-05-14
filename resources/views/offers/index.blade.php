<x-app-layout>
    <div class="flex-1 overflow-y-auto bg-slate-100 px-6 py-6">
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-col gap-4 rounded-[28px] bg-slate-950 px-8 py-7 text-white shadow-xl lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.28em] text-indigo-300">Ofertas</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight">Promociones y beneficios</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Cree ofertas con vigencia, cupos, descuento y alcance comercial para usarlas despues en campañas.</p>
                </div>
                <a href="{{ route('offers.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold uppercase tracking-[0.22em] text-slate-950 transition hover:bg-indigo-50">Nueva oferta</a>
            </div>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700">{{ session('error') }}</div>
            @endif

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm">
                    <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Activas</p>
                    <p class="mt-3 text-4xl font-black text-slate-900">{{ $offers->where('active', true)->count() }}</p>
                    <p class="mt-1 text-sm text-slate-500">Disponibles para campañas</p>
                </div>
                <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm">
                    <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Con cupos</p>
                    <p class="mt-3 text-4xl font-black text-slate-900">{{ $offers->filter(fn ($offer) => is_null($offer->maxRedemptions) || $offer->usedRedemptions < $offer->maxRedemptions)->count() }}</p>
                    <p class="mt-1 text-sm text-slate-500">Aun pueden convertirse</p>
                </div>
                <div class="rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-sm">
                    <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Campañas vinculadas</p>
                    <p class="mt-3 text-4xl font-black text-slate-900">{{ $offers->sum('campaigns_count') }}</p>
                    <p class="mt-1 text-sm text-slate-500">Uso comercial acumulado</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">Catalogo</p>
                        <h2 class="mt-1 text-xl font-black text-slate-900">Ofertas registradas</h2>
                    </div>
                </div>

                @if($offers->isEmpty())
                    <div class="px-6 py-12 text-center text-sm font-medium text-slate-500">Todavia no hay ofertas creadas.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                    <th class="px-6 py-4">Oferta</th>
                                    <th class="px-6 py-4">Servicio</th>
                                    <th class="px-6 py-4">Beneficio</th>
                                    <th class="px-6 py-4">Vigencia</th>
                                    <th class="px-6 py-4">Uso</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                                @foreach($offers as $offer)
                                    <tr>
                                        <td class="px-6 py-4 align-top">
                                            <p class="font-bold text-slate-900">{{ $offer->name }}</p>
                                            <p class="mt-1 max-w-sm text-xs text-slate-500">{{ $offer->description }}</p>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <p>{{ $offer->service?->name ?: 'Sin servicio fijo' }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $offer->specialist?->name ?: 'Cualquier especialista' }}</p>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            @if($offer->discountType === 'PERCENTAGE')
                                                <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.15em] text-indigo-600">{{ $offer->discountValue }}% off</span>
                                            @elseif($offer->discountType === 'FIXED_AMOUNT')
                                                <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.15em] text-emerald-600">${{ number_format((int) $offer->discountValue, 0, ',', '.') }} CLP</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.15em] text-amber-600">Texto libre</span>
                                                <p class="mt-2 text-xs text-slate-500">{{ $offer->customText }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 align-top text-xs text-slate-500">
                                            <p>{{ optional($offer->startsAt)->format('d/m/Y H:i') }}</p>
                                            <p class="mt-1">hasta {{ optional($offer->endsAt)->format('d/m/Y H:i') }}</p>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <p class="font-semibold text-slate-900">{{ $offer->usedRedemptions }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ is_null($offer->maxRedemptions) ? 'Sin limite' : $offer->maxRedemptions . ' cupos' }}</p>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.15em] {{ $offer->active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">{{ $offer->active ? 'Activa' : 'Inactiva' }}</span>
                                        </td>
                                        <td class="px-6 py-4 align-top text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('offers.edit', $offer) }}" class="inline-flex items-center rounded-2xl border border-slate-200 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-700 transition hover:bg-slate-50">Editar</a>
                                                <form action="{{ route('offers.destroy', $offer) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta oferta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center rounded-2xl border border-rose-200 bg-white px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-rose-600 transition hover:bg-rose-50 hover:border-rose-300">Eliminar</button>
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
