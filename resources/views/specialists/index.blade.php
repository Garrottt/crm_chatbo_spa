<x-app-layout>
    <div class="min-h-full overflow-y-auto bg-[linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] px-6 py-6 md:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="rounded-[2rem] bg-slate-900 px-6 py-6 text-white shadow-2xl md:px-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-300">Equipo</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight md:text-4xl">Especialistas</h1>
                        <p class="mt-3 text-sm text-slate-300 md:text-base">
                            Revisa el equipo, sus servicios, disponibilidad y estado operativo en un solo panel.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('specialists.create') }}" class="rounded-2xl bg-white px-5 py-3 text-sm font-bold uppercase tracking-[0.22em] text-slate-900 transition hover:bg-indigo-100">
                            Nuevo especialista
                        </a>
                    </div>
                </div>
            </section>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($specialists as $specialist)
                    <article class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-slate-200/60 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-lg font-black text-slate-900">{{ $specialist->name }}</p>
                                <p class="mt-1 text-sm font-medium text-slate-500">{{ $specialist->user->email ?? 'Sin correo asignado' }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] {{ $specialist->active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                {{ $specialist->active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <div class="mt-5 space-y-3 text-sm">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Especialidad</p>
                                <p class="mt-1 font-medium text-slate-700">{{ $specialist->specialty ?: 'No definida' }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Servicios</p>
                                <p class="mt-1 font-medium text-slate-700">
                                    {{ $specialist->services->pluck('name')->join(', ') ?: 'Sin servicios asignados' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Horario</p>
                                <div class="mt-2 space-y-1">
                                    @forelse($specialist->availabilities as $availability)
                                        <p class="font-medium text-slate-700">
                                            {{ match($availability->dayOfWeek) {1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', default => 'Domingo'} }}:
                                            {{ $availability->startTime }} - {{ $availability->endTime }}
                                        </p>
                                    @empty
                                        <p class="font-medium text-slate-700">Sin horario registrado</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Reservas asignadas</p>
                                <p class="mt-1 font-medium text-slate-700">{{ $specialist->bookings_count }}</p>
                            </div>
                        </div>

                        @if(!empty($specialist->id))
                            <div class="mt-5">
                                <a href="{{ route('specialists.edit', ['specialist' => $specialist->id]) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-indigo-700">
                                    Editar especialista
                                </a>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white/90 px-6 py-12 text-center md:col-span-2 xl:col-span-3">
                        <p class="text-lg font-bold text-slate-700">Aún no hay especialistas registrados</p>
                        <p class="mt-2 text-sm text-slate-500">Crea el primero desde el apartado independiente de alta.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
