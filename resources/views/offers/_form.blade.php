<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    {{-- Banner informativo --}}
    <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-5 py-4 text-sm text-indigo-800">
        <p class="font-semibold">¿Cómo funcionan las ofertas?</p>
        <p class="mt-1 text-xs text-indigo-700">
            Una oferta define el beneficio que recibirá el cliente al venir al spa.
            Cuando creas una campaña de WhatsApp, seleccionas esta oferta y el sistema la usa
            para rellenar <code class="rounded bg-indigo-100 px-1 font-mono">@{{beneficio}}</code> en el mensaje.
            <strong>El descuento se aplica al saldo total el día de la cita en el spa, no en el abono online.</strong>
        </p>
    </div>

    {{-- Sección 1: Identificación --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="mb-4 text-xs font-black uppercase tracking-widest text-slate-400">1 · Identificación interna</h2>
        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="name" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    Nombre de la oferta <span class="text-rose-400">*</span>
                </label>
                <input id="name" name="name" type="text"
                       placeholder="Ej: Descuento 20% en Circuito Spa – Mayo"
                       value="{{ old('name', $offer->name ?? '') }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                <p class="mt-1.5 text-xs text-slate-400">Solo visible para el equipo. Aparece en el selector de ofertas al crear campañas.</p>
                @error('name') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    Notas internas <span class="text-slate-300 font-normal">(opcional)</span>
                </label>
                <textarea id="description" name="description" rows="2"
                          placeholder="Ej: Válida solo para clientes inactivos 30+ días. Autorizada por gerencia."
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">{{ old('description', $offer->description ?? '') }}</textarea>
                <p class="mt-1.5 text-xs text-slate-400">Contexto o condiciones internas. <strong>El cliente nunca ve esto.</strong></p>
                @error('description') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Sección 2: El beneficio --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="mb-1 text-xs font-black uppercase tracking-widest text-slate-400">2 · El beneficio (lo que verá el cliente)</h2>
        <p class="mb-4 text-xs text-slate-500">Este es el dato clave. Aparece como <code class="rounded bg-slate-100 px-1 font-mono text-indigo-600">@{{beneficio}}</code> en el mensaje de WhatsApp de la campaña.</p>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="discountType" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    Tipo de beneficio <span class="text-rose-400">*</span>
                </label>
                <select id="discountType" name="discountType" onchange="updateDiscountUI(this.value)"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    <option value="PERCENTAGE" @selected(old('discountType', $offer->discountType ?? 'PERCENTAGE') === 'PERCENTAGE')>
                        📉 Porcentaje de descuento (ej: 20% off)
                    </option>
                    <option value="FIXED_AMOUNT" @selected(old('discountType', $offer->discountType ?? '') === 'FIXED_AMOUNT')>
                        💵 Monto fijo de descuento (ej: $5.000 CLP)
                    </option>
                    <option value="CUSTOM_TEXT" @selected(old('discountType', $offer->discountType ?? '') === 'CUSTOM_TEXT')>
                        ✍️ Texto libre (ej: Circuito Spa + 30 min gratis)
                    </option>
                </select>
            </div>

            {{-- Valor numérico (solo PERCENTAGE y FIXED_AMOUNT) --}}
            <div id="field-discountValue">
                <label for="discountValue" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    <span id="discountValueLabel">Valor del descuento</span> <span class="text-rose-400">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input id="discountValue" name="discountValue" type="number" min="0"
                           oninput="updateBenefitPreview()"
                           value="{{ old('discountValue', $offer->discountValue ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    <span id="discountUnit" class="whitespace-nowrap text-sm font-bold text-slate-500">%</span>
                </div>
                <p id="discountValueHint" class="mt-1.5 text-xs text-slate-400">Ingresa solo el número (sin símbolo).</p>
            </div>

            {{-- Texto personalizado (solo CUSTOM_TEXT) --}}
            <div id="field-customText" class="md:col-span-2 hidden">
                <label for="customText" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    Texto del beneficio <span class="text-rose-400">*</span>
                </label>
                <input id="customText" name="customText" type="text"
                       oninput="updateBenefitPreview()"
                       placeholder="Ej: Circuito Spa + 30 min de masaje gratis"
                       value="{{ old('customText', $offer->customText ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                <p class="mt-1.5 text-xs text-slate-400">Este texto reemplaza <code class="rounded bg-slate-100 px-1 font-mono text-indigo-600">@{{beneficio}}</code> en el mensaje de WhatsApp.</p>
            </div>

            {{-- Texto adicional para PERCENTAGE / FIXED_AMOUNT --}}
            <div id="field-customTextOptional" class="md:col-span-2">
                <label for="customTextOptional" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    Texto adicional <span class="text-slate-300 font-normal">(opcional)</span>
                </label>
                <input id="customTextOptional" name="customText" type="text"
                       oninput="updateBenefitPreview()"
                       placeholder="Ej: + cortesía de bienvenida incluida"
                       value="{{ old('customText', $offer->customText ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                <p class="mt-1.5 text-xs text-slate-400">Si lo completas, reemplaza el texto del beneficio por este. Si lo dejas vacío, se genera automáticamente (ej: "20% de descuento").</p>
            </div>
        </div>

        {{-- Preview del beneficio --}}
        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
            <p class="mb-1 text-[11px] font-bold uppercase tracking-widest text-emerald-600">Vista previa del mensaje WhatsApp</p>
            <p class="text-sm text-slate-700">
                Hola <strong>Maria</strong>, hace un tiempo no te vemos por <strong>LipoExpress</strong>.
                Tenemos <strong id="benefit-preview" class="text-emerald-700">—</strong> en <strong id="service-preview" class="text-emerald-700">el servicio</strong>.
                Responde "reservar" y te ayudo con una hora.
            </p>
        </div>
    </div>

    {{-- Sección 3: Alcance --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="mb-1 text-xs font-black uppercase tracking-widest text-slate-400">3 · Alcance</h2>
        <p class="mb-4 text-xs text-slate-500">Si vinculas un servicio, el chatbot irá directo a ese servicio cuando el cliente responda la campaña. Si lo dejas sin vincular, el cliente verá el catálogo completo.</p>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="serviceId" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Servicio vinculado</label>
                <select id="serviceId" name="serviceId" onchange="updateBenefitPreview()"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    <option value="">Sin servicio fijo – el cliente elige</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" @selected(old('serviceId', $offer->serviceId ?? '') === $service->id)>{{ $service->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-slate-400">Recomendado: vincula el servicio al que aplica la oferta.</p>
            </div>

            <div>
                <label for="specialistId" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Especialista preferida</label>
                <select id="specialistId" name="specialistId"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    <option value="">Sin especialista fija – la que esté disponible</option>
                    @foreach($specialists as $specialist)
                        <option value="{{ $specialist->id }}" @selected(old('specialistId', $offer->specialistId ?? '') === $specialist->id)>{{ $specialist->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Sección 4: Vigencia y cupos --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="mb-1 text-xs font-black uppercase tracking-widest text-slate-400">4 · Vigencia y cupos</h2>
        <p class="mb-4 text-xs text-slate-500">El sistema desactivará automáticamente la oferta si se superan los cupos o si la fecha de término ya pasó.</p>

        <div class="grid gap-5 md:grid-cols-3">
            <div>
                <label for="startsAt" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    Válida desde <span class="text-rose-400">*</span>
                </label>
                <input id="startsAt" name="startsAt" type="datetime-local"
                       value="{{ old('startsAt', optional($offer->startsAt ?? null)->format('Y-m-d\TH:i')) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                @error('startsAt') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="endsAt" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    Válida hasta <span class="text-rose-400">*</span>
                </label>
                <input id="endsAt" name="endsAt" type="datetime-local"
                       value="{{ old('endsAt', optional($offer->endsAt ?? null)->format('Y-m-d\TH:i')) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                @error('endsAt') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="maxRedemptions" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                    Cupos máximos
                </label>
                <input id="maxRedemptions" name="maxRedemptions" type="number" min="1"
                       placeholder="Sin límite"
                       value="{{ old('maxRedemptions', $offer->maxRedemptions ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                <p class="mt-1.5 text-xs text-slate-400">Deja vacío para cupos ilimitados.</p>
            </div>
        </div>
    </div>

    {{-- Toggle activa --}}
    <label class="flex cursor-pointer items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 transition hover:border-indigo-300">
        <input type="checkbox" name="active" value="1"
               {{ old('active', isset($offer) ? (int) $offer->active : 1) ? 'checked' : '' }}
               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <div>
            <p class="text-sm font-semibold text-slate-700">Oferta activa</p>
            <p class="text-xs text-slate-400">Las ofertas inactivas no pueden seleccionarse en nuevas campañas.</p>
        </div>
    </label>

    {{-- Botones --}}
    <div class="flex flex-col gap-3 sm:flex-row">
        <button type="submit"
                class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-950 px-5 py-4 text-sm font-bold uppercase tracking-[0.24em] text-white transition hover:bg-indigo-700">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('offers.index') }}"
           class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-bold uppercase tracking-[0.2em] text-slate-700 transition hover:bg-slate-50">
            Volver
        </a>
    </div>
</form>

<script>
const currentDiscountType = '{{ old('discountType', $offer->discountType ?? 'PERCENTAGE') }}';
const currentDiscountValue = {{ old('discountValue', $offer->discountValue ?? 0) }};

const servicesData = @json($services->mapWithKeys(fn($s) => [$s->id => $s->name]));

function updateDiscountUI(type) {
    const fieldValue    = document.getElementById('field-discountValue');
    const fieldCustom   = document.getElementById('field-customText');
    const fieldOptional = document.getElementById('field-customTextOptional');
    const unitLabel     = document.getElementById('discountUnit');
    const valueLabel    = document.getElementById('discountValueLabel');
    const valueHint     = document.getElementById('discountValueHint');

    if (type === 'CUSTOM_TEXT') {
        fieldValue.classList.add('hidden');
        fieldCustom.classList.remove('hidden');
        fieldOptional.classList.add('hidden');
        // Mover el name al campo correcto
        document.getElementById('customText').name = 'customText';
        document.getElementById('customTextOptional').name = '_customTextOptional';
    } else {
        fieldValue.classList.remove('hidden');
        fieldCustom.classList.add('hidden');
        fieldOptional.classList.remove('hidden');
        // Mover el name al campo correcto
        document.getElementById('customText').name = '_customText';
        document.getElementById('customTextOptional').name = 'customText';

        if (type === 'PERCENTAGE') {
            unitLabel.textContent = '%';
            valueLabel.textContent = 'Porcentaje de descuento';
            valueHint.textContent = 'Ingresa solo el número. Ej: 20 = 20% de descuento.';
        } else {
            unitLabel.textContent = 'CLP';
            valueLabel.textContent = 'Monto de descuento';
            valueHint.textContent = 'Ingresa el monto en pesos. Ej: 5000 = $5.000 de descuento.';
        }
    }
    updateBenefitPreview();
}

function updateBenefitPreview() {
    const type  = document.getElementById('discountType').value;
    const val   = parseInt(document.getElementById('discountValue')?.value || 0);
    const custom = type === 'CUSTOM_TEXT'
        ? document.getElementById('customText').value.trim()
        : document.getElementById('customTextOptional').value.trim();
    const preview = document.getElementById('benefit-preview');
    const servicePreview = document.getElementById('service-preview');
    const serviceId = document.getElementById('serviceId').value;

    let text = '—';
    if (type === 'CUSTOM_TEXT') {
        text = custom || 'tu texto aquí...';
    } else if (custom) {
        text = custom;
    } else if (type === 'PERCENTAGE' && val > 0) {
        text = val + '% de descuento';
    } else if (type === 'FIXED_AMOUNT' && val > 0) {
        text = '$' + val.toLocaleString('es-CL') + ' CLP de descuento';
    }
    preview.textContent = text;
    servicePreview.textContent = servicesData[serviceId] || 'nuestros servicios';
}

document.addEventListener('DOMContentLoaded', function () {
    updateDiscountUI(currentDiscountType);
});
</script>
