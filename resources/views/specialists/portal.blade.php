<x-app-layout>
    @push('styles')
    <style>
        .specialist-panel-shell {
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.10), transparent 24%),
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.09), transparent 18%),
                linear-gradient(180deg, #f5f7fb 0%, #eef3fb 100%);
        }

        .specialist-panel-card {
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 42px rgba(148, 163, 184, 0.14);
        }

        .specialist-panel-soft-card {
            border: 1px solid rgba(255, 255, 255, 0.72);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 42px rgba(148, 163, 184, 0.12);
        }

        .specialist-panel-hero {
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.18), transparent 26%),
                linear-gradient(180deg, #121a31 0%, #0d1428 100%);
            box-shadow: 0 26px 58px rgba(15, 23, 42, 0.18);
        }

        .specialist-panel-info-tile {
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.03);
        }

        .specialist-panel-stat-icon {
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .specialist-panel-table-row + .specialist-panel-table-row {
            border-top: 1px solid #f1f5f9;
        }

        .specialist-panel-service-tile {
            border: 1px solid #e7edf6;
            background: #ffffff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92);
        }

        .specialist-summary-stat {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .specialist-summary-stat + .specialist-summary-stat {
            border-left: 1px solid #edf2f7;
            padding-left: 1.6rem;
        }

        .specialist-summary-icon {
            display: flex;
            height: 3.3rem;
            width: 3.3rem;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        .specialist-summary-value {
            font-size: 1.95rem;
            line-height: 1;
            font-weight: 900;
            color: #0f172a;
        }

        .specialist-summary-value.is-wide {
            font-size: 1.15rem;
            line-height: 1.18;
        }

        .specialist-summary-label {
            margin-top: 0.28rem;
            font-size: 0.95rem;
            color: #64748b;
        }

        .specialist-panel-inner-band {
            padding-left: 0.65rem;
            padding-right: 0.65rem;
        }

        .specialist-hero-layout {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
        }

        .specialist-hero-copy {
            min-width: 0;
        }

        .specialist-hero-title {
            font-size: clamp(2.45rem, 3.35vw, 4rem);
            line-height: 0.96;
            letter-spacing: -0.04em;
        }

        .specialist-hero-subtitle {
            max-width: 44rem;
            font-size: 0.96rem;
            line-height: 1.55;
        }

        .specialist-hero-info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .specialist-hero-tile {
            min-width: 0;
        }

        .specialist-hero-tile-value {
            font-size: 0.98rem;
            line-height: 1.25;
            overflow-wrap: normal;
            word-break: normal;
        }

        .specialist-hero-tile-value.is-email {
            font-size: clamp(0.8rem, 0.82vw, 0.92rem);
            line-height: 1.2;
            white-space: nowrap;
            letter-spacing: -0.01em;
        }

        @media (max-width: 1024px) {
            .specialist-panel-grid-two {
                grid-template-columns: 1fr;
            }

            .specialist-panel-inner-band {
                padding-left: 0;
                padding-right: 0;
            }

            .specialist-summary-stat + .specialist-summary-stat {
                border-left: 0;
                border-top: 1px solid #edf2f7;
                padding-left: 0;
                padding-top: 1rem;
            }
        }

        @media (min-width: 900px) {
            .specialist-hero-info-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .specialist-hero-layout {
                display: grid;
                grid-template-columns: minmax(0, 1.02fr) minmax(600px, 0.98fr);
                align-items: start;
            }
        }

        @media (max-width: 768px) {
            .specialist-hero-title {
                font-size: 2.35rem;
            }

            .specialist-hero-subtitle {
                font-size: 0.92rem;
                line-height: 1.55;
            }

            .specialist-hero-tile-value.is-email {
                white-space: normal;
                overflow-wrap: anywhere;
                word-break: break-word;
            }
        }
    </style>
    @endpush

    <div class="specialist-panel-shell h-full overflow-y-auto px-6 py-6 md:px-8">
        <div class="mx-auto max-w-[1480px] space-y-5">

            <!-- Hero Section -->
            <section class="specialist-panel-hero rounded-[2rem] px-7 py-5 text-white md:px-9 md:py-5 xl:px-10 xl:py-5">
                <div class="specialist-hero-layout">
                    <div class="specialist-hero-copy">
                        <p class="text-[11px] font-black uppercase tracking-[0.35em] text-indigo-300">Portal especialista</p>
                        <h1 class="specialist-hero-title mt-2 font-black text-white">{{ $specialist->name }}</h1>
                        <p class="specialist-hero-subtitle mt-2.5 text-slate-300">
                            Revise sus proximas citas, disponibilidad semanal y servicios asignados desde un solo lugar.
                        </p>
                    </div>

                    <div class="specialist-hero-info-grid">
                        <div class="specialist-panel-info-tile specialist-hero-tile rounded-[1.25rem] px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-[1.05rem] border border-white/10 bg-white/5 text-white/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 2l1.6 4.94H19l-4.3 3.12L16.3 15 12 11.88 7.7 15l1.6-4.94L5 6.94h5.4L12 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Especialidad</p>
                                    <p class="specialist-hero-tile-value mt-2 font-black text-white">{{ $specialist->specialty ?: 'Sin especialidad definida' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="specialist-panel-info-tile specialist-hero-tile rounded-[1.25rem] px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-[1.05rem] border border-emerald-400/15 bg-emerald-400/10 text-emerald-300">
                                    <span class="h-3 w-3 rounded-full bg-emerald-400 shadow-[0_0_16px_rgba(16,185,129,0.45)]"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Estado</p>
                                    <p class="specialist-hero-tile-value mt-2 font-black {{ $specialist->active ? 'text-emerald-300' : 'text-amber-300' }}">
                                        {{ $specialist->active ? 'Activo' : 'Inactivo' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="specialist-panel-info-tile specialist-hero-tile rounded-[1.25rem] px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-[1.05rem] border border-white/10 bg-white/5 text-white/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Acceso</p>
                                    <p class="specialist-hero-tile-value is-email mt-2 font-black text-white">{{ $specialist->user?->email ?: 'Sin correo asociado' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats -->
            <section class="specialist-panel-inner-band">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <article class="specialist-panel-soft-card rounded-[1.7rem] px-6 py-6">
                        <div class="flex items-center gap-4">
                            <div class="specialist-panel-stat-icon flex h-16 w-16 items-center justify-center rounded-full bg-violet-100 text-violet-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">Servicios asignados</p>
                                <p class="mt-2 text-4xl font-black text-slate-900">{{ $stats['services'] }}</p>
                                <p class="mt-1 text-base text-slate-500">Servicios activos</p>
                            </div>
                        </div>
                    </article>

                    <article class="specialist-panel-soft-card rounded-[1.7rem] px-6 py-6">
                        <div class="flex items-center gap-4">
                            <div class="specialist-panel-stat-icon flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">Citas de hoy</p>
                                <p class="mt-2 text-4xl font-black text-slate-900">{{ $stats['today'] }}</p>
                                <p class="mt-1 text-base text-slate-500">Reservas programadas</p>
                            </div>
                        </div>
                    </article>

                    <article class="specialist-panel-soft-card rounded-[1.7rem] px-6 py-6">
                        <div class="flex items-center gap-4">
                            <div class="specialist-panel-stat-icon flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">Pendientes hoy</p>
                                <p class="mt-2 text-4xl font-black text-slate-900">{{ $stats['pending'] }}</p>
                                <p class="mt-1 text-base text-slate-500">Esperando confirmacion</p>
                            </div>
                        </div>
                    </article>

                    <article class="specialist-panel-soft-card rounded-[1.7rem] px-6 py-6">
                        <div class="flex items-center gap-4">
                            <div class="specialist-panel-stat-icon flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3v18h18M7 14l3-3 3 2 5-5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">Citas esta semana</p>
                                <p class="mt-2 text-4xl font-black text-slate-900">{{ $stats['week'] }}</p>
                                <p class="mt-1 text-base text-slate-500">Total programadas</p>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Siguiente Cita + Resumen del día -->
            <section class="specialist-panel-grid-two grid gap-4 xl:grid-cols-[1.05fr,1fr]">
                <article class="specialist-panel-card rounded-[1.9rem] border-l-[4px] border-l-emerald-500 px-6 py-6 md:px-7">
                    <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">Siguiente cita</p>
                    @if($nextBooking)
                        <div class="mt-5 grid gap-5 lg:grid-cols-[1.2fr,0.9fr] lg:items-center">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-2xl font-black text-emerald-700">
                                    {{ strtoupper(substr($nextBooking->client->name ?? 'C', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h2 class="truncate text-4xl font-black tracking-tight text-slate-900">{{ $nextBooking->client->name ?? 'Cliente sin nombre' }}</h2>
                                    <p class="mt-1 text-xl font-semibold text-slate-600">{{ $nextBooking->service->name ?? 'Servicio sin nombre' }}</p>
                                    <div class="mt-3 flex items-center gap-2 text-base font-medium text-slate-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ optional($nextBooking->scheduledAt)->timezone('America/Santiago')->locale('es')->translatedFormat('l d \\d\\e F') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-200 pt-4 text-center lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
                                <span class="inline-flex rounded-full bg-emerald-100 px-5 py-2 text-xs font-black uppercase tracking-[0.24em] text-emerald-700">Confirmada</span>
                                <p class="mt-4 text-5xl font-black tracking-tight text-slate-900">
                                    {{ optional($nextBooking->scheduledAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }}
                                    -
                                    {{ optional($nextBooking->endAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="mt-5 rounded-[1.35rem] border border-dashed border-slate-300 px-5 py-10 text-center text-base font-semibold text-slate-500">
                            No hay una proxima cita programada.
                        </div>
                    @endif
                </article>

                <article class="specialist-panel-card rounded-[1.9rem] px-6 py-5 md:px-7">
                    <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">Resumen del dia</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <div class="specialist-summary-stat">
                            <div class="specialist-summary-icon bg-violet-100 text-violet-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="specialist-summary-value">{{ $todaySummary['appointments'] }}</p>
                                <p class="specialist-summary-label">Total de citas</p>
                            </div>
                        </div>

                        <div class="specialist-summary-stat">
                            <div class="specialist-summary-icon bg-sky-100 text-sky-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="specialist-summary-value">{{ $todaySummary['occupiedHours'] }}</p>
                                <p class="specialist-summary-label">Horas ocupadas</p>
                            </div>
                        </div>

                        <div class="specialist-summary-stat">
                            <div class="specialist-summary-icon bg-emerald-100 text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="specialist-summary-value is-wide">{{ $todaySummary['statusLabel'] }}</p>
                                <p class="specialist-summary-label">{{ $todaySummary['statusHint'] }}</p>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <!-- Citas de hoy + Disponibilidad semanal -->
            <section class="specialist-panel-grid-two grid items-stretch gap-4 xl:grid-cols-[1.08fr,1fr]">

                <!-- CITAS DE HOY -->
                <article class="specialist-panel-card self-stretch rounded-[1.9rem] px-6 py-6 md:px-7">
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 mb-5">Citas de hoy</p>

                    <div class="grid grid-cols-[120px_1fr_1fr_120px] gap-4 px-4 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                        <span>Hora</span>
                        <span>Cliente</span>
                        <span>Servicio</span>
                        <span class="text-center">Estado</span>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        @forelse($todayBookings as $booking)
                            <div class="specialist-panel-table-row grid grid-cols-[120px_1fr_1fr_120px] items-center gap-4 px-4 py-4">
                                <div class="text-sm font-bold text-slate-800">
                                    {{ optional($booking->scheduledAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }}
                                    -
                                    {{ optional($booking->endAt)->timezone('America/Santiago')->locale('es')->translatedFormat('H:i') }}
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-600">
                                        {{ strtoupper(substr($booking->client->name ?? 'C', 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700">{{ $booking->client->name ?? 'Cliente sin nombre' }}</span>
                                </div>

                                <div class="text-sm font-medium text-slate-600">{{ $booking->service->name ?? 'Servicio' }}</div>

                                <div class="text-center">
                                    <span class="inline-flex rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.1em] {{ strtoupper($booking->status) === 'CONFIRMED' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ strtoupper($booking->status) === 'CONFIRMED' ? 'Confirmada' : 'Pendiente' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-sm font-medium text-slate-500">
                                No tiene citas agendadas para hoy.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-5 text-center">
                        <a href="{{ route('agenda') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-indigo-600 transition hover:text-indigo-500">
                            Ver todas las citas de hoy
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </article>

                <!-- DISPONIBILIDAD SEMANAL -->
                <article class="specialist-panel-card self-stretch rounded-[1.9rem] px-6 py-6 md:px-7">
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 mb-4">Disponibilidad semanal</p>

                    <div class="mt-2 divide-y divide-slate-100">
                        @php($enabledAvailability = collect($availability)->filter(fn ($slot) => $slot['enabled'])->values())
                        @forelse($enabledAvailability as $slot)
                            <div class="flex w-full flex-row items-center justify-between gap-2 py-3">
                                <div class="flex w-32 shrink-0 items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-bold text-slate-700">{{ $slot['day'] }}</span>
                                </div>

                                <span class="flex-1 text-center text-sm font-medium text-slate-500">{{ $slot['hours'] }}</span>

                                <span class="inline-flex w-28 shrink-0 items-center justify-center rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-600">
                                    Disponible
                                </span>
                            </div>
                        @empty
                            <div class="rounded-[1.2rem] border border-dashed border-slate-300 px-4 py-8 text-center text-sm font-semibold text-slate-500">
                                No hay disponibilidad semanal configurada.
                            </div>
                        @endforelse
                    </div>
                </article>

            </section>

            <!-- Servicios Asignados -->
            <section class="specialist-panel-card rounded-[1.9rem] px-6 py-6 md:px-7">
                <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">Servicios asignados</p>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @forelse($specialist->services as $service)
                        <article class="specialist-panel-service-tile flex items-center justify-between gap-4 rounded-[1.35rem] px-5 py-5">
                            <div class="flex min-w-0 items-center gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xl font-black text-slate-900">{{ $service->name }}</h3>
                                    <p class="mt-1 text-sm leading-snug text-slate-500">{{ $service->description ?: 'Servicio asignado al especialista.' }}</p>
                                </div>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2 pl-4">
                                <span class="rounded-full bg-slate-100 px-4 py-1.5 text-xs font-black uppercase tracking-[0.18em] text-slate-600">{{ $service->durationMinutes }} min</span>
                                <span class="rounded-full bg-emerald-100 px-4 py-1.5 text-xs font-black uppercase tracking-[0.18em] text-emerald-700">${{ number_format((float) $service->price, 0, ',', '.') }} CLP</span>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[1.35rem] border border-dashed border-slate-300 px-5 py-10 text-center text-base font-semibold text-slate-500 md:col-span-2">
                            Todavia no tiene servicios asignados.
                        </div>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>