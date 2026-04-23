<x-app-layout>
    @push('styles')
    <style>
        .agenda-shell {
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.10), transparent 28%),
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.12), transparent 24%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }

        .agenda-card,
        .agenda-stat-card {
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.10);
        }

        .fc {
            --fc-border-color: #e2e8f0;
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: #f8fafc;
            --fc-list-event-hover-bg-color: #eef2ff;
            --fc-today-bg-color: rgba(99, 102, 241, 0.08);
            --fc-event-bg-color: #4f46e5;
            --fc-event-border-color: #4338ca;
            --fc-event-text-color: #ffffff;
            --fc-button-bg-color: #1e293b;
            --fc-button-border-color: #1e293b;
            --fc-button-hover-bg-color: #0f172a;
            --fc-button-hover-border-color: #0f172a;
            --fc-button-active-bg-color: #4f46e5;
            --fc-button-active-border-color: #4f46e5;
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .fc .fc-toolbar-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: capitalize;
        }

        .fc .fc-button {
            border-radius: 0.9rem;
            padding: 0.7rem 1rem;
            font-weight: 700;
            box-shadow: none;
        }

        .fc .fc-scrollgrid,
        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #e2e8f0;
        }

        .fc .fc-col-header-cell {
            background: #f8fafc;
            color: #334155;
            font-weight: 800;
            text-transform: capitalize;
            padding: 0.65rem 0;
        }

        .fc .fc-timegrid-slot {
            height: 3.6rem;
        }

        .fc .fc-timegrid-axis,
        .fc .fc-timegrid-slot-label {
            color: #64748b;
            font-weight: 700;
            background: #fff;
        }

        .fc .fc-timegrid-now-indicator-line {
            border-color: #ef4444;
        }

        .fc .fc-timegrid-now-indicator-arrow {
            border-color: #ef4444;
            color: #ef4444;
        }

        .fc .fc-event {
            border-radius: 0.9rem;
            padding: 0.2rem 0.35rem;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.24);
            border: none;
        }

        .fc-event-highlight-new {
            animation: agendaPulseNew 1.8s ease-in-out 2;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25), 0 18px 30px rgba(16, 185, 129, 0.28) !important;
        }

        .fc-event-highlight-updated {
            animation: agendaPulseUpdated 1.8s ease-in-out 2;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25), 0 18px 30px rgba(245, 158, 11, 0.28) !important;
        }

        @keyframes agendaPulseNew {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.03); }
        }

        @keyframes agendaPulseUpdated {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.025); }
        }

        .fc-event-card {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            padding: 0.35rem 0.45rem;
        }

        .fc-event-time {
            font-size: 0.68rem;
            font-weight: 800;
            opacity: 0.9;
            letter-spacing: 0.02em;
        }

        .fc-event-client {
            font-size: 0.78rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .fc-event-service {
            font-size: 0.7rem;
            opacity: 0.92;
            line-height: 1.1;
        }

        .fc-event-specialist {
            font-size: 0.68rem;
            opacity: 0.86;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border-radius: 9999px;
            padding: 0.45rem 0.8rem;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .status-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
        }

        .status-confirmed {
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
        }

        .status-confirmed .status-dot {
            background: #10b981;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
        }

        .status-pending .status-dot {
            background: #f59e0b;
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.12);
            color: #b91c1c;
        }

        .status-cancelled .status-dot {
            background: #ef4444;
        }

        .status-default {
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
        }

        .status-default .status-dot {
            background: #3b82f6;
        }

        .agenda-detail-panel {
            background:
                radial-gradient(circle at top right, rgba(99, 102, 241, 0.12), transparent 34%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .agenda-action {
            border-radius: 1rem;
            padding: 0.8rem 1rem;
            font-weight: 800;
            transition: all 180ms ease;
        }

        .agenda-action:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .agenda-feedback {
            display: none;
        }

        .agenda-feedback.is-visible {
            display: block;
        }

        .agenda-inline-panel {
            display: none;
        }

        .agenda-inline-panel.is-visible {
            display: block;
        }

        .detail-metric {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.95rem 1rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid #e2e8f0;
        }

        .detail-metric-label {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .detail-metric-value {
            text-align: right;
            font-size: 0.94rem;
            font-weight: 700;
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .fc .fc-toolbar-title {
                font-size: 1.2rem;
            }

            .fc .fc-button {
                padding: 0.55rem 0.75rem;
                font-size: 0.85rem;
            }
        }
    </style>
    @endpush

    <div class="agenda-shell h-full overflow-y-auto px-6 py-6 md:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="rounded-[2rem] bg-slate-900 px-6 py-6 text-white shadow-2xl md:px-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-300">Spa Ikigai</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight md:text-4xl">Agenda de Reservas</h1>
                        <p class="mt-3 text-sm text-slate-300 md:text-base">
                            Organiza citas, visualiza la carga semanal y gestiona cambios sin salir del calendario.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-400">Vista</p>
                            <p class="mt-2 text-lg font-bold">Semanal</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-400">Horario</p>
                            <p class="mt-2 text-lg font-bold">08:00 - 20:00</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur col-span-2 md:col-span-1">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-400">Estado</p>
                            <p class="mt-2 text-lg font-bold text-emerald-300">Operativo</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur col-span-2 md:col-span-3">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-400">Actualizacion</p>
                            <p id="agenda-refresh-status" class="mt-2 text-sm font-bold text-slate-200">Sincronizacion automatica cada 15 segundos</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="agenda-stat-card rounded-[1.75rem] border border-white/70 bg-white/90 px-5 py-5 backdrop-blur">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Bloques del calendario</p>
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <div>
                            <p id="stat-total" class="text-3xl font-black text-slate-900">0</p>
                            <p class="mt-1 text-sm text-slate-500">En el rango del calendario</p>
                        </div>
                        <div class="rounded-2xl bg-indigo-100 p-3 text-indigo-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </article>

                <article class="agenda-stat-card rounded-[1.75rem] border border-emerald-100 bg-white/90 px-5 py-5 backdrop-blur">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Confirmadas</p>
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <div>
                            <p id="stat-confirmed" class="text-3xl font-black text-emerald-600">0</p>
                            <p class="mt-1 text-sm text-slate-500">Listas para atender</p>
                        </div>
                        <div class="rounded-2xl bg-emerald-100 p-3 text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </article>

                <article class="agenda-stat-card rounded-[1.75rem] border border-amber-100 bg-white/90 px-5 py-5 backdrop-blur">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Pendientes</p>
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <div>
                            <p id="stat-pending" class="text-3xl font-black text-amber-500">0</p>
                            <p class="mt-1 text-sm text-slate-500">Esperando confirmación</p>
                        </div>
                        <div class="rounded-2xl bg-amber-100 p-3 text-amber-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </article>

                <article class="agenda-stat-card rounded-[1.75rem] border border-rose-100 bg-white/90 px-5 py-5 backdrop-blur">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Canceladas</p>
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <div>
                            <p id="stat-cancelled" class="text-3xl font-black text-rose-500">0</p>
                            <p class="mt-1 text-sm text-slate-500">Bloques liberados</p>
                        </div>
                        <div class="rounded-2xl bg-rose-100 p-3 text-rose-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </article>
            </section>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="agenda-card rounded-[2rem] border border-white/70 bg-white/90 p-4 backdrop-blur md:p-6">
                    <div id="calendar" class="rounded-3xl bg-white p-2 md:p-4"></div>
                </div>

                <aside class="agenda-detail-panel agenda-card rounded-[2rem] border border-white/70 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Detalle de reserva</p>
                            <h2 id="detail-title" class="mt-3 text-2xl font-black text-slate-900">Resumen del dia</h2>
                        </div>
                        <div class="rounded-2xl bg-slate-900 px-3 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white">
                            Agenda
                        </div>
                    </div>

                    <div class="mt-5">
                        <span id="detail-status" class="status-badge status-default">
                            <span class="status-dot"></span>
                            Vista general
                        </span>
                    </div>

                    <div id="detail-feedback" class="agenda-feedback mt-4 rounded-2xl border px-4 py-3 text-sm font-semibold"></div>

                    <div class="mt-6 space-y-4">
                        <div class="space-y-3">
                            <div class="detail-metric">
                                <span id="detail-client-label" class="detail-metric-label">Proxima cita</span>
                                <span id="detail-client" class="detail-metric-value">Sin citas proximas</span>
                            </div>

                            <div class="detail-metric">
                                <span id="detail-service-label" class="detail-metric-label">Especialistas en turno</span>
                                <span id="detail-service" class="detail-metric-value">Sin agenda activa</span>
                            </div>

                            <div class="detail-metric">
                                <span id="detail-start-label" class="detail-metric-label">Nivel de ocupacion</span>
                                <span id="detail-start" class="detail-metric-value">0%</span>
                            </div>

                            <div class="detail-metric">
                                <span id="detail-end-label" class="detail-metric-label">Bloques de hoy</span>
                                <span id="detail-end" class="detail-metric-value">0 reservas</span>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                            <p id="detail-summary" class="text-sm leading-6 text-slate-600">
                                Este panel resume la operacion del dia. Selecciona una reserva para ver su detalle completo y gestionar cambios.
                            </p>
                        </div>

                        @if(auth()->user()?->isAdmin())
                            <div class="grid gap-3">
                                <button id="action-confirm" type="button" class="agenda-action bg-emerald-500 text-white hover:bg-emerald-600">
                                    Confirmar reserva
                                </button>
                                <button id="action-cancel" type="button" class="agenda-action bg-rose-500 text-white hover:bg-rose-600">
                                    Cancelar reserva
                                </button>
                                <button id="action-reschedule" type="button" class="agenda-action bg-indigo-600 text-white hover:bg-indigo-700">
                                    Reagendar y avisar al cliente
                                </button>
                            </div>

                            <div id="cancel-panel" class="agenda-inline-panel rounded-2xl border border-rose-200 bg-white px-4 py-4">
                                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-rose-400">Motivo de cancelación</p>
                                <textarea id="cancel-reason" rows="4" placeholder="Ej: Tuvimos un ajuste interno de horario, disculpa las molestias." class="mt-3 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-rose-400"></textarea>
                                <div class="mt-3 flex gap-3">
                                    <button id="submit-cancel" type="button" class="agenda-action flex-1 bg-rose-500 text-white hover:bg-rose-600">Enviar cancelación</button>
                                    <button id="close-cancel-panel" type="button" class="agenda-action border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">Cerrar</button>
                                </div>
                            </div>

                            <div id="reschedule-panel" class="agenda-inline-panel rounded-2xl border border-indigo-200 bg-white px-4 py-4">
                                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-indigo-400">Nueva fecha de reserva</p>
                                <div class="mt-3 space-y-3">
                                    <label class="block">
                                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Nuevo inicio</span>
                                        <input id="reschedule-start" type="datetime-local" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500">
                                    </label>
                                    <label class="block">
                                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Nuevo fin</span>
                                        <input id="reschedule-end" type="datetime-local" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500">
                                    </label>
                                </div>
                                <div class="mt-3 flex gap-3">
                                    <button id="submit-reschedule" type="button" class="agenda-action flex-1 bg-indigo-600 text-white hover:bg-indigo-700">Confirmar reagendado</button>
                                    <button id="close-reschedule-panel" type="button" class="agenda-action border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">Cerrar</button>
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4">
                                <p class="text-sm leading-6 text-slate-600">
                                    Estás viendo solo las reservas asignadas a tu agenda como especialista.
                                </p>
                            </div>
                        @endif
                    </div>
                </aside>
            </section>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var statTotal = document.getElementById('stat-total');
            var statConfirmed = document.getElementById('stat-confirmed');
            var statPending = document.getElementById('stat-pending');
            var statCancelled = document.getElementById('stat-cancelled');
            var detailTitle = document.getElementById('detail-title');
            var detailStatus = document.getElementById('detail-status');
            var detailClientLabel = document.getElementById('detail-client-label');
            var detailClient = document.getElementById('detail-client');
            var detailServiceLabel = document.getElementById('detail-service-label');
            var detailService = document.getElementById('detail-service');
            var detailStartLabel = document.getElementById('detail-start-label');
            var detailStart = document.getElementById('detail-start');
            var detailEndLabel = document.getElementById('detail-end-label');
            var detailEnd = document.getElementById('detail-end');
            var detailSummary = document.getElementById('detail-summary');
            var detailFeedback = document.getElementById('detail-feedback');
            var cancelPanel = document.getElementById('cancel-panel');
            var reschedulePanel = document.getElementById('reschedule-panel');
            var rescheduleStart = document.getElementById('reschedule-start');
            var rescheduleEnd = document.getElementById('reschedule-end');
            var cancelReason = document.getElementById('cancel-reason');
            var actionConfirm = document.getElementById('action-confirm');
            var actionCancel = document.getElementById('action-cancel');
            var actionReschedule = document.getElementById('action-reschedule');
            var submitCancel = document.getElementById('submit-cancel');
            var submitReschedule = document.getElementById('submit-reschedule');
            var closeCancelPanel = document.getElementById('close-cancel-panel');
            var closeReschedulePanel = document.getElementById('close-reschedule-panel');
            var refreshStatus = document.getElementById('agenda-refresh-status');
            var activeEvent = null;
            var isAdmin = @json(auth()->user()?->isAdmin());
            var autoRefreshTimer = null;
            var eventSnapshot = new Map();
            var lastRefreshDiff = { created: 0, updated: 0 };
            var calendar = null;

            function formatDateTime(date) {
                if (!date) return '-';

                return new Intl.DateTimeFormat('es-CL', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    hour: '2-digit',
                    minute: '2-digit'
                }).format(date);
            }

            function toInputValue(date) {
                if (!date) return '';

                var tzOffset = date.getTimezoneOffset() * 60000;
                return new Date(date.getTime() - tzOffset).toISOString().slice(0, 16);
            }

            function statusMeta(status) {
                var normalized = (status || '').toUpperCase();

                if (normalized === 'CONFIRMED') {
                    return { label: 'Confirmada', className: 'status-confirmed' };
                }

                if (normalized === 'PENDING') {
                    return { label: 'Pendiente', className: 'status-pending' };
                }

                if (normalized === 'CANCELLED') {
                    return { label: 'Cancelada', className: 'status-cancelled' };
                }

                return { label: normalized || 'Sin estado', className: 'status-default' };
            }

            function setButtonsDisabled(disabled) {
                if (!isAdmin) return;

                actionConfirm.disabled = disabled;
                actionCancel.disabled = disabled;
                actionReschedule.disabled = disabled;
                submitCancel.disabled = disabled;
                submitReschedule.disabled = disabled;
            }

            function hideActionPanels() {
                if (!isAdmin) return;
                cancelPanel.classList.remove('is-visible');
                reschedulePanel.classList.remove('is-visible');
            }

            function showCancelPanel() {
                if (!isAdmin) return;
                hideActionPanels();
                cancelPanel.classList.add('is-visible');
                cancelReason.focus();
            }

            function showReschedulePanel() {
                if (!isAdmin) return;
                hideActionPanels();
                reschedulePanel.classList.add('is-visible');
                rescheduleStart.focus();
            }

            function showFeedback(message, tone) {
                var tones = {
                    success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
                    error: 'border-rose-200 bg-rose-50 text-rose-700',
                    info: 'border-indigo-200 bg-indigo-50 text-indigo-700'
                };

                detailFeedback.className = 'agenda-feedback is-visible mt-4 rounded-2xl border px-4 py-3 text-sm font-semibold ' + (tones[tone] || tones.info);
                detailFeedback.textContent = message;
            }

            function clearFeedback() {
                detailFeedback.className = 'agenda-feedback mt-4 rounded-2xl border px-4 py-3 text-sm font-semibold';
                detailFeedback.textContent = '';
            }

            function setRefreshStatus(message) {
                if (refreshStatus) {
                    refreshStatus.textContent = message;
                }
            }

            function buildEventFingerprint(event) {
                return JSON.stringify({
                    id: event.id,
                    start: event.start ? event.start.toISOString() : null,
                    end: event.end ? event.end.toISOString() : null,
                    status: event.extendedProps.status || null,
                    title: event.title || null
                });
            }

            function updateEventSnapshot(events) {
                var nextSnapshot = new Map();

                events.forEach(function(event) {
                    nextSnapshot.set(event.id, buildEventFingerprint(event));
                });

                eventSnapshot = nextSnapshot;
            }

            function detectEventChanges(events) {
                if (eventSnapshot.size === 0) {
                    updateEventSnapshot(events);
                    return { createdIds: [], updatedIds: [] };
                }

                var createdIds = [];
                var updatedIds = [];

                events.forEach(function(event) {
                    var fingerprint = buildEventFingerprint(event);
                    var previousFingerprint = eventSnapshot.get(event.id);

                    if (!previousFingerprint) {
                        createdIds.push(event.id);
                        return;
                    }

                    if (previousFingerprint !== fingerprint) {
                        updatedIds.push(event.id);
                    }
                });

                updateEventSnapshot(events);

                return { createdIds: createdIds, updatedIds: updatedIds };
            }

            function highlightChangedEvents(diff) {
                if (!diff) {
                    return;
                }

                window.requestAnimationFrame(function() {
                    diff.createdIds.forEach(function(eventId) {
                        calendarEl.querySelectorAll('[data-agenda-event-id="' + eventId + '"]').forEach(function(node) {
                            node.classList.remove('fc-event-highlight-updated');
                            node.classList.add('fc-event-highlight-new');
                            window.setTimeout(function() {
                                node.classList.remove('fc-event-highlight-new');
                            }, 4200);
                        });
                    });

                    diff.updatedIds.forEach(function(eventId) {
                        calendarEl.querySelectorAll('[data-agenda-event-id="' + eventId + '"]').forEach(function(node) {
                            node.classList.remove('fc-event-highlight-new');
                            node.classList.add('fc-event-highlight-updated');
                            window.setTimeout(function() {
                                node.classList.remove('fc-event-highlight-updated');
                            }, 4200);
                        });
                    });
                });
            }

            function updateStats(events) {
                var totals = {
                    total: events.length,
                    confirmed: 0,
                    pending: 0,
                    cancelled: 0
                };

                events.forEach(function(event) {
                    var status = (event.extendedProps.status || '').toUpperCase();

                    if (status === 'CONFIRMED') totals.confirmed += 1;
                    if (status === 'PENDING') totals.pending += 1;
                    if (status === 'CANCELLED') totals.cancelled += 1;
                });

                statTotal.textContent = totals.total;
                statConfirmed.textContent = totals.confirmed;
                statPending.textContent = totals.pending;
                statCancelled.textContent = totals.cancelled;
            }

            function sameDay(left, right) {
                return left.getFullYear() === right.getFullYear()
                    && left.getMonth() === right.getMonth()
                    && left.getDate() === right.getDate();
            }

            function formatClock(date) {
                if (!date) return '--:--';

                return new Intl.DateTimeFormat('es-CL', {
                    hour: '2-digit',
                    minute: '2-digit'
                }).format(date);
            }

            function getTodaySummary(events) {
                var referenceDate = calendar ? calendar.getDate() : new Date();
                var now = new Date();
                var todaysEvents = events.filter(function(event) {
                    return event.start && sameDay(event.start, referenceDate);
                });

                var todaysActiveEvents = todaysEvents.filter(function(event) {
                    return (event.extendedProps.status || '').toUpperCase() !== 'CANCELLED';
                });

                var nextAppointment = todaysActiveEvents
                    .filter(function(event) { return event.start >= now; })
                    .sort(function(left, right) { return left.start - right.start; })[0]
                    || todaysActiveEvents.sort(function(left, right) { return left.start - right.start; })[0]
                    || null;

                var specialists = Array.from(new Set(
                    todaysActiveEvents
                        .map(function(event) { return event.extendedProps.specialist || null; })
                        .filter(Boolean)
                ));

                var occupiedMinutes = todaysActiveEvents.reduce(function(total, event) {
                    if (!event.start || !event.end) {
                        return total;
                    }

                    return total + Math.max(0, Math.round((event.end - event.start) / 60000));
                }, 0);

                var occupancy = Math.min(100, Math.round((occupiedMinutes / (12 * 60)) * 100));

                return {
                    nextAppointment: nextAppointment,
                    specialists: specialists,
                    occupancy: occupancy,
                    totalBlocks: todaysEvents.length
                };
            }

            function renderDaySummary() {
                var summary = getTodaySummary(calendar.getEvents());
                var nextText = summary.nextAppointment
                    ? formatClock(summary.nextAppointment.start) + ' - ' + (summary.nextAppointment.extendedProps.client || 'Cliente')
                    : 'Sin citas proximas';
                var specialistsText = summary.specialists.length
                    ? summary.specialists.join(', ')
                    : 'Sin especialistas asignados';

                activeEvent = null;
                clearFeedback();
                detailTitle.textContent = 'Resumen del dia';
                detailStatus.className = 'status-badge status-default';
                detailStatus.innerHTML = '<span class="status-dot"></span>Vista general';
                detailClientLabel.textContent = 'Proxima cita';
                detailClient.textContent = nextText;
                detailServiceLabel.textContent = 'Especialistas en turno';
                detailService.textContent = specialistsText;
                detailStartLabel.textContent = 'Nivel de ocupacion';
                detailStart.textContent = summary.occupancy + '%';
                detailEndLabel.textContent = 'Bloques de hoy';
                detailEnd.textContent = summary.totalBlocks + (summary.totalBlocks === 1 ? ' reserva' : ' reservas');
                detailSummary.textContent = 'Resumen rapido del dia actual. Selecciona una reserva para ver el cliente, servicio, especialista y acciones disponibles.';
                hideActionPanels();
            }

            function updateDetail(event) {
                if (!event) return;

                activeEvent = event;
                clearFeedback();

                var meta = statusMeta(event.extendedProps.status);
                var endDate = event.end || event.start;

                detailTitle.textContent = event.title || 'Reserva seleccionada';
                detailStatus.className = 'status-badge ' + meta.className;
                detailStatus.innerHTML = '<span class="status-dot"></span>' + meta.label;
                detailClientLabel.textContent = 'Cliente';
                detailClient.textContent = event.extendedProps.client || 'Sin cliente';
                detailServiceLabel.textContent = 'Servicio';
                detailService.textContent = event.extendedProps.service || 'Sin servicio';
                detailStartLabel.textContent = 'Inicio';
                detailStart.textContent = formatDateTime(event.start);
                detailEndLabel.textContent = 'Fin';
                detailEnd.textContent = formatDateTime(endDate);
                detailSummary.textContent = 'Reserva para ' + (event.extendedProps.client || 'cliente sin nombre') + ' en el servicio "' + (event.extendedProps.service || 'Sin servicio') + '" con ' + (event.extendedProps.specialist || 'especialista por asignar') + '. Estado actual: ' + meta.label + '.';
                if (isAdmin) {
                    rescheduleStart.value = toInputValue(event.start);
                    rescheduleEnd.value = toInputValue(endDate);
                    cancelReason.value = '';
                    hideActionPanels();
                }
            }

            function applyBookingUpdate(payload) {
                if (!activeEvent || !payload || !payload.booking) return;

                var booking = payload.booking;
                activeEvent.setStart(booking.start);
                activeEvent.setEnd(booking.end);
                activeEvent.setProp('title', booking.title);
                activeEvent.setProp('backgroundColor', booking.color);
                activeEvent.setProp('borderColor', booking.color);
                activeEvent.setExtendedProp('status', booking.status);
                activeEvent.setExtendedProp('client', booking.client);
                activeEvent.setExtendedProp('clientId', booking.clientId);
                activeEvent.setExtendedProp('service', booking.service);
                activeEvent.setExtendedProp('specialist', booking.specialist);
                updateDetail(activeEvent);
                updateStats(calendar.getEvents());
            }

            function syncActiveEventSelection() {
                if (!activeEvent) {
                    return;
                }

                var refreshedEvent = calendar.getEventById(activeEvent.id);

                if (!refreshedEvent) {
                    renderDaySummary();
                    showFeedback('La reserva que estabas viendo ya no aparece en la vista actual de la agenda.', 'info');
                    return;
                }

                activeEvent = refreshedEvent;
                updateDetail(refreshedEvent);
            }

            function refetchCalendarEvents(showIndicator) {
                if (showIndicator) {
                    setRefreshStatus('Actualizando agenda...');
                }

                calendar.refetchEvents();
            }

            async function patchBooking(url, body, successMessage) {
                if (!activeEvent) {
                    showFeedback('Primero selecciona una reserva.', 'error');
                    return;
                }

                setButtonsDisabled(true);
                showFeedback('Procesando cambio...', 'info');

                try {
                    var response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(body || {})
                    });

                    var payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'No se pudo actualizar la reserva.');
                    }

                    applyBookingUpdate(payload);
                    showFeedback(payload.message || successMessage, 'success');
                } catch (error) {
                    showFeedback(error.message || 'Ocurrió un error al actualizar la reserva.', 'error');
                } finally {
                    setButtonsDisabled(false);
                }
            }

            if (isAdmin) {
                actionConfirm.addEventListener('click', function() {
                    if (!activeEvent) {
                        showFeedback('Primero selecciona una reserva.', 'error');
                        return;
                    }

                    patchBooking('/api/bookings/' + activeEvent.id + '/confirm', {}, 'Reserva confirmada.');
                });

                actionCancel.addEventListener('click', function() {
                    if (!activeEvent) {
                        showFeedback('Primero selecciona una reserva.', 'error');
                        return;
                    }

                    showCancelPanel();
                });

                submitCancel.addEventListener('click', function() {
                    if (!activeEvent) {
                        showFeedback('Primero selecciona una reserva.', 'error');
                        return;
                    }

                    if (!cancelReason.value.trim()) {
                        showFeedback('Escribe un motivo antes de cancelar la reserva.', 'error');
                        cancelReason.focus();
                        return;
                    }

                    patchBooking('/api/bookings/' + activeEvent.id + '/cancel', {
                        reason: cancelReason.value
                    }, 'Reserva cancelada.');
                });

                actionReschedule.addEventListener('click', function() {
                    if (!activeEvent) {
                        showFeedback('Primero selecciona una reserva.', 'error');
                        return;
                    }

                    showReschedulePanel();
                });

                submitReschedule.addEventListener('click', function() {
                    if (!activeEvent) {
                        showFeedback('Primero selecciona una reserva.', 'error');
                        return;
                    }

                    if (!rescheduleStart.value || !rescheduleEnd.value) {
                        showFeedback('Completa la nueva fecha y hora antes de reagendar.', 'error');
                        return;
                    }

                    patchBooking('/api/bookings/' + activeEvent.id + '/reschedule', {
                        scheduledAt: rescheduleStart.value,
                        endAt: rescheduleEnd.value
                    }, 'Reserva reagendada.');
                });

                closeCancelPanel.addEventListener('click', function() {
                    cancelReason.value = '';
                    hideActionPanels();
                });

                closeReschedulePanel.addEventListener('click', function() {
                    hideActionPanels();
                });
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'es',
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                height: 'auto',
                allDaySlot: false,
                expandRows: true,
                nowIndicator: true,
                stickyHeaderDates: true,
                firstDay: 1,
                events: '/api/calendar-events',
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },
                eventContent: function(arg) {
                    var startTime = arg.timeText || '';
                    var client = arg.event.extendedProps.client || arg.event.title || 'Reserva';
                    var service = arg.event.extendedProps.service || '';
                    var specialist = arg.event.extendedProps.specialist || 'Sin asignar';

                    return {
                        html: `
                            <div class="fc-event-card">
                                <div class="fc-event-time">${startTime}</div>
                                <div class="fc-event-client">${client}</div>
                                <div class="fc-event-service">${service}</div>
                                <div class="fc-event-specialist">👩‍⚕️ ${specialist}</div>
                            </div>
                        `
                    };
                },
                eventClick: function(info) {
                    updateDetail(info.event);
                },
                eventDidMount: function(info) {
                    info.el.setAttribute('data-agenda-event-id', info.event.id);
                },
                datesSet: function() {
                    updateStats(calendar.getEvents());
                    if (!activeEvent) {
                        renderDaySummary();
                    }
                },
                eventsSet: function(events) {
                    var diff = detectEventChanges(events);
                    updateStats(events);
                    syncActiveEventSelection();
                    if (!activeEvent) {
                        renderDaySummary();
                    }
                    highlightChangedEvents(diff);
                    lastRefreshDiff = {
                        created: diff.createdIds.length,
                        updated: diff.updatedIds.length
                    };

                    var timestamp = new Intl.DateTimeFormat('es-CL', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }).format(new Date());

                    if (lastRefreshDiff.created || lastRefreshDiff.updated) {
                        setRefreshStatus(
                            'Ultima sincronizacion: ' + timestamp
                            + ' · Nuevas: ' + lastRefreshDiff.created
                            + ' · Cambios: ' + lastRefreshDiff.updated
                        );
                    } else {
                        setRefreshStatus('Ultima sincronizacion: ' + timestamp + ' · Sin cambios recientes');
                    }

                    if (activeEvent && diff.updatedIds.includes(activeEvent.id)) {
                        showFeedback('La reserva seleccionada fue actualizada automaticamente.', 'info');
                    }

                    if (activeEvent && diff.createdIds.includes(activeEvent.id)) {
                        showFeedback('La reserva seleccionada acaba de aparecer en la agenda.', 'info');
                    }
                }
            });

            calendar.render();
            renderDaySummary();

            autoRefreshTimer = window.setInterval(function() {
                refetchCalendarEvents(true);
            }, 15000);

            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    return;
                }

                refetchCalendarEvents(true);
            });
        });
    </script>
    @endpush
</x-app-layout>
