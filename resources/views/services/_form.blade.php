<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="name" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Nombre</label>
            <input id="name" name="name" type="text" value="{{ old('name', $service->name ?? '') }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            @error('name') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
            <p class="mt-2 text-xs font-medium text-slate-500">El codigo se genera automaticamente desde el nombre y la moneda se guarda siempre en CLP.</p>
        </div>

        <div>
            <label for="durationMinutes" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Duracion (min)</label>
            <input id="durationMinutes" name="durationMinutes" type="number" min="1" value="{{ old('durationMinutes', $service->durationMinutes ?? 60) }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            @error('durationMinutes') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="price" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Precio</label>
            <input id="price" name="price" type="number" min="0" value="{{ old('price', $service->price ?? 0) }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            @error('price') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="description" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Descripcion</label>
        <textarea id="description" name="description" rows="5" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">{{ old('description', $service->description ?? '') }}</textarea>
        @error('description') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
        <input type="checkbox" name="active" value="1" {{ old('active', isset($service) ? (int) $service->active : 1) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        Servicio activo
    </label>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-950 px-5 py-4 text-sm font-bold uppercase tracking-[0.24em] text-white transition hover:bg-indigo-700">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-bold uppercase tracking-[0.2em] text-slate-700 transition hover:bg-slate-50">
            Volver
        </a>
    </div>
</form>
