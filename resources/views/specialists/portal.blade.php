<x-app-layout>
    <div class="h-full overflow-y-auto bg-slate-100 px-6 py-6 md:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="rounded-[2rem] bg-slate-900 px-6 py-6 text-white shadow-2xl md:px-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-300">Portal especialista</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight md:text-4xl">{{ $specialist->name }}</h1>
                        <p class="mt-3 text-sm text-slate-300 md:text-base">
                            Revise sus proximas citas, servicios asignados y disponibilidad semanal desde un solo lugar.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-400">Especialidad</p>
                            <p class="mt-2 text-sm font-semibold text-slate-100">{{ $specialist->specialty ?: 'Sin especialidad definida' }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-400">Estado</p>
                            <p class="mt-2 text-sm font-semibold {{ $specialist->active ? 'text-emerald-300' : 'text-amber-300' }}">
                                {{ $specialist->active ? 'Activo' : 'Inactivo' }}
                            </p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur sm:col-span-2">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-400">Acceso</p>
                            <p class="mt-2 text-sm font-semibold text-slate-100">{{ $specialist->user?->email ?: 'Sin correo asociado' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-[1.75rem] border border-white/70 bg-white px-5 py-5 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Servicios asignados</p>
                    <p class="mt-4 text-3xl font-black text-slate-900">{{ $stats['services'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Configurados para su perfil</p>
                </article>
                <article class="rounded-[1.75rem] border border-emerald-100 bg-white px-5 py-5 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Citas de hoy</p>
                    <p class="mt-4 text-3xl font-black text-emerald-600">{{ $stats['today'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Reservas dentro del dia</p>
                </article>
                <article class="rounded-[1.75rem] border border-amber-100 bg-white px-5 py-5 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Pendientes hoy</p>
                    <p class="mt-4 text-3xl font-black text-amber-500">{{ $stats['pending'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Esperando confirmacion</p>
                </article>
                <article class="rounded-[1.75rem] border border-indigo-100 bg-white px-5 py-5 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Citas esta semana</p>
                    <p class="mt-4 text-3xl font-black text-indigo-600">{{ $stats['week'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">No canceladas</p>
                </article>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.3fr,0.9fr]">
                <div class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Proximas reservas</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-900">Agenda inmediata</h2>
                        </div>
                        <a href="{{ route('agenda') }}" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                            Ver mi agenda
                        </a>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse($upcomingBookings as $booking)
                            <div class="rounded-2xl border border-slate-200 px-4 py-4">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <p class="text-lg font-bold text-slate-900">{{ $booking->client->name ?? 'Cliente sin nombre' }}</p>
                                        <p class="mt-1 text-sm font-medium text-slate-500">{{ $booking->service->name ?? 'Servicio sin nombre' }}</p>
                                    </div>
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.2em]
                                        {{ strtoupper($booking->status) === 'CONFIRMED' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ strtoupper($booking->status) === 'CONFIRMED' ? 'Confirmada' : 'Pendiente' }}
                                    </span>
                                </div>
                                <div class="mt-4 grid gap-3 text-sm text-slate-600 md:grid-cols-2">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Inicio</p>
                                        <p class="mt-1 font-semibold text-slate-900">{{ optional($booking->scheduledAt)->timezone('America/Santiago')->locale('es')->translatedFormat('l d \\d\\e F, H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Termino</p>
                                        <p class="mt-1 font-semibold text-slate-900">{{ optional($booking->endAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm font-semibold text-slate-500">
                                No tiene reservas proximas asignadas.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-6">
                    <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Siguiente cita</p>
                        @if($nextBooking)
                            <h2 class="mt-3 text-2xl font-black text-slate-900">{{ $nextBooking->client->name ?? 'Cliente sin nombre' }}</h2>
                            <p class="mt-2 text-sm font-semibold text-slate-500">{{ $nextBooking->service->name ?? 'Servicio sin nombre' }}</p>
                            <div class="mt-4 rounded-2xl bg-slate-900 px-4 py-4 text-white">
                                <p class="text-sm font-semibold">{{ optional($nextBooking->scheduledAt)->timezone('America/Santiago')->locale('es')->translatedFormat('l d \\d\\e F') }}</p>
                                <p class="mt-1 text-2xl font-black">{{ optional($nextBooking->scheduledAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }} - {{ optional($nextBooking->endAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }}</p>
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm font-semibold text-slate-500">
                                No hay una proxima cita programada.
                            </div>
                        @endif
                    </section>

                    <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Citas de hoy</p>
                        <div class="mt-4 space-y-3">
                            @forelse($todayBookings as $booking)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="font-bold text-slate-900">{{ optional($booking->scheduledAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }} - {{ optional($booking->endAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }}</p>
                                    <p class="mt-1 text-sm font-medium text-slate-600">{{ $booking->client->name ?? 'Cliente sin nombre' }} · {{ $booking->service->name ?? 'Servicio' }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm font-semibold text-slate-500">
                                    No tiene citas agendadas para hoy.
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.05fr,0.95fr]">
                <div class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Servicios asignados</p>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @forelse($specialist->services as $service)
                            <article class="rounded-2xl border border-slate-200 px-4 py-4">
                                <h3 class="text-lg font-bold text-slate-900">{{ $service->name }}</h3>
                                @if(!empty($service->description))
                                    <p class="mt-2 text-sm text-slate-500">{{ $service->description }}</p>
                                @endif
                                <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                                    <span class="rounded-full bg-slate-100 px-3 py-2">{{ $service->durationMinutes }} min</span>
                                    <span class="rounded-full bg-emerald-100 px-3 py-2 text-emerald-700">${{ number_format((float) $service->price, 0, ',', '.') }} CLP</span>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm font-semibold text-slate-500 md:col-span-2">
                                Todavia no tiene servicios asignados.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Disponibilidad semanal</p>
                    <div class="mt-5 space-y-3">
                        @foreach($availability as $slot)
                            <div class="flex items-center justify-between rounded-2xl border px-4 py-3 {{ $slot['enabled'] ? 'border-emerald-100 bg-emerald-50/60' : 'border-slate-200 bg-slate-50' }}">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $slot['day'] }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $slot['hours'] }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] {{ $slot['enabled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $slot['enabled'] ? 'Disponible' : 'Libre' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
