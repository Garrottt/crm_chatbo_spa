@php
    $editing = isset($specialist) && $specialist;
    $selectedServices = old('serviceIds', $editing ? $specialist->services->pluck('id')->all() : []);
    $availabilityMap = $editing ? $specialist->availabilities->keyBy('dayOfWeek') : collect();
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="name" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Nombre</label>
            <input id="name" name="name" type="text" value="{{ old('name', $editing ? $specialist->name : '') }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            @error('name') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="specialty" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Especialidad</label>
            <input id="specialty" name="specialty" type="text" value="{{ old('specialty', $editing ? $specialist->specialty : '') }}" placeholder="Ej: Masoterapia, estética facial" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            @error('specialty') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Correo de acceso</label>
            <input id="email" name="email" type="email" value="{{ old('email', $editing ? ($specialist->user->email ?? '') : '') }}" {{ $editing ? '' : 'required' }} class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            <p class="mt-2 text-xs text-slate-500">
                {{ $editing ? 'Déjalo igual o vacío si no quieres cambiar/crear el acceso ahora.' : 'Se usará para que el especialista inicie sesión.' }}
            </p>
            @error('email') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">
                {{ $editing ? 'Nueva contraseña' : 'Contraseña inicial' }}
            </label>
            <input id="password" name="password" type="password" {{ $editing ? '' : 'required' }} class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            <p class="mt-2 text-xs text-slate-500">{{ $editing ? 'Déjala vacía si no quieres cambiarla. Si agregas correo nuevo para crear acceso, aquí debes definir la contraseña.' : 'Mínimo 8 caracteres.' }}</p>
            @error('password') <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Servicios asignados</label>
        <div class="grid gap-2 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-3 md:grid-cols-2">
            @foreach($services as $service)
                <label class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 text-sm font-medium text-slate-700">
                    <input type="checkbox" name="serviceIds[]" value="{{ $service->id }}" {{ in_array($service->id, $selectedServices) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>{{ $service->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <div class="mb-3">
            <label class="block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Disponibilidad semanal</label>
            <p class="mt-2 text-sm text-slate-500">Activa solo los días en que el especialista atiende y define su horario.</p>
        </div>

        <div class="space-y-3">
            @foreach($days as $dayIndex => $dayLabel)
                @php
                    $currentAvailability = $availabilityMap->get($dayIndex);
                    $enabled = old("availabilities.$dayIndex.enabled", $currentAvailability ? 1 : 0);
                    $startTime = old("availabilities.$dayIndex.startTime", $currentAvailability->startTime ?? '');
                    $endTime = old("availabilities.$dayIndex.endTime", $currentAvailability->endTime ?? '');
                @endphp
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="availabilities[{{ $dayIndex }}][enabled]" value="1" {{ $enabled ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            {{ $dayLabel }}
                        </label>

                        <div class="grid grid-cols-2 gap-3 md:w-[250px]">
                            <input type="time" name="availabilities[{{ $dayIndex }}][startTime]" value="{{ $startTime }}" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500">
                            <input type="time" name="availabilities[{{ $dayIndex }}][endTime]" value="{{ $endTime }}" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
        <input type="checkbox" name="active" value="1" {{ old('active', $editing ? (int) $specialist->active : 1) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        Especialista activo
    </label>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-950 px-5 py-4 text-sm font-bold uppercase tracking-[0.24em] text-white transition hover:bg-indigo-700">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('specialists.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-bold uppercase tracking-[0.2em] text-slate-700 transition hover:bg-slate-50">
            Volver
        </a>
    </div>
</form>
