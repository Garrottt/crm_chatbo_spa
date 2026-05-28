<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    {{-- Fila 1: Nombre + Objetivo --}}
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="name" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                Nombre interno <span class="text-rose-400">*</span>
            </label>
            <input id="name" name="name" type="text"
                   placeholder="Ej: Reactivación mayo 2025"
                   value="{{ old('name', $campaign->name ?? '') }}" required
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            <p class="mt-1.5 text-xs text-slate-400">Solo visible para el equipo. No lo verá el cliente.</p>
            @error('name') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="objective" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                Objetivo <span class="text-rose-400">*</span>
            </label>
            <input id="objective" name="objective" type="text"
                   placeholder="Ej: Reactivación, Fidelización, Promoción…"
                   value="{{ old('objective', $campaign->objective ?? '') }}" required
                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            <p class="mt-1.5 text-xs text-slate-400">Describe brevemente qué quieres lograr con esta campaña.</p>
            @error('objective') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Fila 2: Oferta + Segmento --}}
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="offerId" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                Oferta vinculada <span class="text-rose-400">*</span>
            </label>
            <select id="offerId" name="offerId" required
                    onchange="updateOfferPreview(this.value)"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                <option value="">Seleccione una oferta</option>
                @foreach($offers as $offer)
                    <option value="{{ $offer->id }}" @selected(old('offerId', $campaign->offerId ?? '') === $offer->id)>{{ $offer->name }}</option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-slate-400">La oferta define el descuento o beneficio que recibirán los clientes.</p>
            @error('offerId') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="segmentType" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                ¿A quién va dirigida? <span class="text-rose-400">*</span>
            </label>
            <select id="segmentType" name="segmentType" required
                    onchange="updateSegmentFields(this.value)"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                @foreach([
                    'inactive_30'         => 'Clientes inactivos (últimos 30 días)',
                    'inactive_90'         => 'Clientes inactivos (últimos 90 días)',
                    'frequent'            => 'Clientes frecuentes',
                    'consulted_no_booking'=> 'Consultaron pero no reservaron',
                ] as $value => $label)
                    <option value="{{ $value }}" @selected(old('segmentType', $campaign->segmentType ?? 'inactive_30') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Campo contextual: Días de inactividad (solo para segmentos de inactividad y sin reserva) --}}
    <div id="field-lookbackDays" class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5 hidden">
        <label for="lookbackDays" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-indigo-500">
            Buscar clientes sin visita en los últimos...
        </label>
        <div class="flex items-center gap-3">
            <input id="lookbackDays" name="lookbackDays" type="number" min="1" max="365"
                   value="{{ old('lookbackDays', data_get($campaign->segmentFilter ?? [], 'lookbackDays', 30)) }}"
                   class="w-32 rounded-xl border border-indigo-200 bg-white px-4 py-3 text-lg font-bold text-slate-900 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            <span class="text-sm font-semibold text-slate-600">días</span>
        </div>
        <p class="mt-2 text-xs text-indigo-600">
            💡 Solo se contactará a clientes que <strong>no han reservado ni visitado el spa</strong> en este periodo.
            Ej: 30 = clientes que llevan más de 30 días sin venir.
        </p>
    </div>

    {{-- Campo contextual: Mínimo de reservas (solo para clientes frecuentes) --}}
    <div id="field-minBookings" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 hidden">
        <label for="minBookings" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-emerald-600">
            Número mínimo de reservas completadas
        </label>
        <div class="flex items-center gap-3">
            <input id="minBookings" name="minBookings" type="number" min="1"
                   value="{{ old('minBookings', data_get($campaign->segmentFilter ?? [], 'minBookings', 3)) }}"
                   class="w-32 rounded-xl border border-emerald-200 bg-white px-4 py-3 text-lg font-bold text-slate-900 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
            <span class="text-sm font-semibold text-slate-600">reservas</span>
        </div>
        <p class="mt-2 text-xs text-emerald-700">
            💡 Solo se incluirán clientes que hayan completado <strong>al menos este número de reservas</strong>.
            Ej: 3 = clientes que han venido 3 o más veces.
        </p>
    </div>

    {{-- Mensaje --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
        <label for="messageTemplate" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
            Mensaje que recibirá el cliente <span class="text-rose-400">*</span>
        </label>

        @php
            $varButtons = [
                ['var' => '{{nombre}}',    'label' => '👤 Nombre',     'desc' => 'Nombre del cliente'],
                ['var' => '{{negocio}}',   'label' => '🏢 Negocio',    'desc' => 'Nombre del spa'],
                ['var' => '{{oferta}}',    'label' => '🏷️ Oferta',     'desc' => 'Nombre de la oferta'],
                ['var' => '{{beneficio}}', 'label' => '🎁 Beneficio',   'desc' => 'El descuento o beneficio'],
                ['var' => '{{servicio}}',  'label' => '💆 Servicio',    'desc' => 'Nombre del servicio'],
            ];
        @endphp
        <div class="mb-3 flex flex-wrap gap-2">
            @foreach($varButtons as $btn)
            <button type="button"
                    onclick="insertVariable('{{ $btn['var'] }}')"
                    title="{{ $btn['desc'] }}"
                    class="rounded-lg border border-indigo-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-600 hover:text-white hover:border-indigo-600">
                {{ $btn['label'] }} <code class="ml-1 opacity-60">{{ $btn['var'] }}</code>
            </button>
            @endforeach
        </div>

        <textarea id="messageTemplate" name="messageTemplate" rows="5" required
                  oninput="updatePreview()"
                  placeholder="Ej: Hola @{{nombre}}, hace un tiempo no te vemos por @{{negocio}}. Tenemos @{{beneficio}} en @{{servicio}}. Responde 'reservar' y te ayudo con una hora."
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">{{ old('messageTemplate', $campaign->messageTemplate ?? '') }}</textarea>
        @error('messageTemplate') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror

        {{-- Preview --}}
        <div class="mt-4">
            <p class="mb-2 text-[11px] font-bold uppercase tracking-widest text-slate-400">Vista previa del mensaje</p>
            <div id="msg-preview"
                 class="min-h-[60px] rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap">
                <span class="text-slate-300 italic">El preview aparecerá aquí mientras escribes...</span>
            </div>
        </div>
    </div>

    {{-- Cooldown --}}
    <label class="flex cursor-pointer items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 transition hover:border-amber-300">
        <input type="checkbox" name="ignoreCooldown" value="1"
               {{ old('ignoreCooldown', data_get($campaign->segmentFilter ?? [], 'ignoreCooldown', false)) ? 'checked' : '' }}
               class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <div>
            <p class="text-sm font-semibold text-amber-800">Contactar aunque hayan recibido mensajes recientemente</p>
            <p class="mt-1 text-xs text-amber-700">
                Por defecto, el sistema <strong>excluye automáticamente</strong> a clientes que ya recibieron una campaña en los últimos 30 días (para no saturarlos).
                Marca esta opción solo si necesitas contactarlos de todas formas, por ejemplo para una promoción urgente.
            </p>
        </div>
    </label>

    {{-- Botones --}}
    <div class="flex flex-col gap-3 sm:flex-row">
        <button type="submit"
                class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-950 px-5 py-4 text-sm font-bold uppercase tracking-[0.24em] text-white transition hover:bg-indigo-700">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('campaigns.index') }}"
           class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-bold uppercase tracking-[0.2em] text-slate-700 transition hover:bg-slate-50">
            Volver
        </a>
    </div>
</form>

<script>
@php
$offersJson = $offers->mapWithKeys(function ($o) {
    if ($o->customText) {
        $benefit = $o->customText;
    } elseif ($o->discountType === 'PERCENTAGE' && $o->discountValue) {
        $benefit = $o->discountValue . '% de descuento';
    } elseif ($o->discountType === 'FIXED_AMOUNT' && $o->discountValue) {
        $benefit = '$' . number_format($o->discountValue, 0, ',', '.') . ' CLP de descuento';
    } else {
        $benefit = 'Promoción especial';
    }
    return [$o->id => [
        'benefit' => $benefit,
        'service' => $o->service?->name ?? 'el servicio',
        'oferta'  => $o->name,
    ]];
});
@endphp

// Datos de ofertas para el preview dinámico
const offersData = @json($offersJson);

// Valores base del preview
const previewValues = {
    '@{{nombre}}':    'María',
    '@{{negocio}}':   'LipoExpress',
    '@{{oferta}}':    'Promo Mayo',
    '@{{beneficio}}': '20% de descuento',
    '@{{servicio}}':  'el servicio',
};

function updateOfferPreview(offerId) {
    const offer = offersData[offerId];
    if (offer) {
        previewValues['@{{beneficio}}'] = offer.benefit;
        previewValues['@{{servicio}}']  = offer.service;
        previewValues['@{{oferta}}']    = offer.oferta;
    } else {
        previewValues['@{{beneficio}}'] = '...';
        previewValues['@{{servicio}}']  = '...';
        previewValues['@{{oferta}}']    = '...';
    }
    updatePreview();
}

function insertVariable(variable) {
    const ta = document.getElementById('messageTemplate');
    const start = ta.selectionStart;
    const end   = ta.selectionEnd;
    ta.value = ta.value.slice(0, start) + variable + ta.value.slice(end);
    ta.selectionStart = ta.selectionEnd = start + variable.length;
    ta.focus();
    updatePreview();
}

function updatePreview() {
    const raw = document.getElementById('messageTemplate').value.trim();
    const preview = document.getElementById('msg-preview');
    if (!raw) {
        preview.innerHTML = '<span class="text-slate-300 italic">El preview aparecerá aquí mientras escribes...</span>';
        return;
    }
    let rendered = raw;
    for (const [key, val] of Object.entries(previewValues)) {
        rendered = rendered.replaceAll(key, `<strong class="text-indigo-600">${val}</strong>`);
    }
    preview.innerHTML = rendered;
}

function updateSegmentFields(segment) {
    const lookback  = document.getElementById('field-lookbackDays');
    const minBook   = document.getElementById('field-minBookings');
    const showLook  = ['inactive_30', 'inactive_90', 'consulted_no_booking'].includes(segment);
    const showMin   = segment === 'frequent';
    lookback.classList.toggle('hidden', !showLook);
    minBook.classList.toggle('hidden', !showMin);
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function () {
    updateSegmentFields(document.getElementById('segmentType').value);
    // Cargar datos de la oferta ya seleccionada (al editar)
    updateOfferPreview(document.getElementById('offerId').value);
    updatePreview();
});
</script>
