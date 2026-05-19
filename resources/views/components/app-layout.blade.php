<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRM Spa La Roca</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-100">

    <div class="crm-app-shell flex h-dvh overflow-hidden pb-16 lg:pb-0">
        <div class="hidden w-16 flex-col items-center py-6 bg-indigo-900 text-white shadow-xl z-20 lg:flex">
            <div class="mb-8 p-2 bg-white rounded-lg">
                <span class="text-indigo-900 font-black text-xl">SR</span>
            </div>

            @if(auth()->user()?->isAdmin())
                <a href="/chat"
                   class="p-3 mb-4 rounded-xl transition-all duration-300 {{ request()->is('chat*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}"
                   title="Ver Chats">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </a>
            @endif

            @if(auth()->user()?->isSpecialist())
                <a href="/mi-panel"
                   class="p-3 mb-4 rounded-xl transition-all duration-300 {{ request()->is('mi-panel*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}"
                   title="Mi Panel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" />
                    </svg>
                </a>
            @endif

            <a href="/agenda"
               class="p-3 mb-4 rounded-xl transition-all duration-300 {{ request()->is('agenda*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}"
               title="Ver Agenda">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </a>

            @if(auth()->user()?->isAdmin())
                <a href="/dashboard"
                   class="p-3 mb-4 rounded-xl transition-all duration-300 {{ request()->is('dashboard*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}"
                   title="Ver Estadísticas">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </a>
            @endif

            @if(auth()->user()?->isAdmin())
                <a href="/services"
                   class="p-3 mb-4 rounded-xl transition-all duration-300 {{ request()->is('services*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}"
                   title="Ver Servicios">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </a>
            @endif

            @if(auth()->user()?->isAdmin())
                <a href="/specialists"
                   class="p-3 mb-4 rounded-xl transition-all duration-300 {{ request()->is('specialists*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}"
                   title="Ver Especialistas">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H11a4 4 0 00-4 4v2m10 0H7m10-12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </a>
            @endif
            @if(auth()->user()?->isAdmin())
                <a href="/offers"
                   class="p-3 mb-4 rounded-xl transition-all duration-300 {{ request()->is('offers*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}"
                   title="Ver Ofertas">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3-1.343-3-3h6c0 1.657-1.343 3-3 3zm0 0l7 4v5a2 2 0 01-2 2H7a2 2 0 01-2-2v-5l7-4z" />
                    </svg>
                </a>
            @endif

            @if(auth()->user()?->isAdmin())
                <a href="/campaigns"
                   class="p-3 mb-4 rounded-xl transition-all duration-300 {{ request()->is('campaigns*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}"
                   title="Ver Campanas">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10" />
                    </svg>
                </a>
            @endif

            <div class="mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-3 text-indigo-400 transition hover:text-white" title="Cerrar sesión">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <main class="relative flex-1 flex flex-col overflow-hidden min-w-0">
            @auth
                <div class="absolute right-4 top-4 z-40 sm:right-6 sm:top-5" data-alert-widget>
                    <button type="button" data-alert-toggle class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-700 shadow-lg shadow-slate-300/50 ring-1 ring-slate-200 transition hover:bg-slate-50" title="Alertas">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                        </svg>
                        <span data-alert-count class="absolute -right-1 -top-1 hidden min-w-5 rounded-full bg-rose-500 px-1.5 py-0.5 text-center text-[10px] font-black text-white">0</span>
                    </button>

                    <div data-alert-panel class="hidden absolute right-0 mt-3 w-[calc(100vw-2rem)] max-w-[22rem] overflow-hidden rounded-[1.5rem] bg-white shadow-2xl shadow-slate-300/60 ring-1 ring-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">Alertas</p>
                                <h2 class="text-base font-black text-slate-950">Notificaciones recientes</h2>
                            </div>
                            <button type="button" data-alert-read-all class="text-[10px] font-black uppercase tracking-[0.18em] text-indigo-600 hover:text-indigo-500">
                                Leer todas
                            </button>
                        </div>
                        <div data-alert-list class="max-h-96 overflow-y-auto"></div>
                        <a href="{{ route('alerts.index') }}" class="block border-t border-slate-100 px-5 py-3 text-center text-xs font-black uppercase tracking-[0.2em] text-slate-700 hover:bg-slate-50">
                            Ver historial
                        </a>
                    </div>
                </div>
            @endauth

            {{ $slot }}
        </main>

        @auth
            <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-indigo-800/70 bg-indigo-950/95 px-2 py-2 text-white shadow-[0_-16px_40px_rgba(15,23,42,0.22)] backdrop-blur lg:hidden">
                <div class="mx-auto grid max-w-xl {{ auth()->user()?->isAdmin() ? 'grid-cols-5' : 'grid-cols-3' }} gap-1">
                    @if(auth()->user()?->isAdmin())
                        <a href="/chat" class="flex flex-col items-center justify-center rounded-xl px-2 py-2 text-[10px] font-bold {{ request()->is('chat*') ? 'bg-indigo-700 text-white' : 'text-indigo-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mb-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                            Chat
                        </a>
                    @else
                        <a href="/mi-panel" class="flex flex-col items-center justify-center rounded-xl px-2 py-2 text-[10px] font-bold {{ request()->is('mi-panel*') ? 'bg-indigo-700 text-white' : 'text-indigo-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mb-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" /></svg>
                            Panel
                        </a>
                    @endif

                    <a href="/agenda" class="flex flex-col items-center justify-center rounded-xl px-2 py-2 text-[10px] font-bold {{ request()->is('agenda*') ? 'bg-indigo-700 text-white' : 'text-indigo-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Agenda
                    </a>

                    @if(auth()->user()?->isAdmin())
                        <a href="/dashboard" class="flex flex-col items-center justify-center rounded-xl px-2 py-2 text-[10px] font-bold {{ request()->is('dashboard*') ? 'bg-indigo-700 text-white' : 'text-indigo-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mb-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2" /></svg>
                            Stats
                        </a>
                        <a href="/services" class="flex flex-col items-center justify-center rounded-xl px-2 py-2 text-[10px] font-bold {{ request()->is('services*') ? 'bg-indigo-700 text-white' : 'text-indigo-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mb-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            Servicios
                        </a>
                        <a href="/campaigns" class="flex flex-col items-center justify-center rounded-xl px-2 py-2 text-[10px] font-bold {{ request()->is('campaigns*') || request()->is('offers*') || request()->is('specialists*') ? 'bg-indigo-700 text-white' : 'text-indigo-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mb-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10" /></svg>
                            Mas
                        </a>
                    @else
                        <form method="POST" action="{{ route('logout') }}" class="contents">
                            @csrf
                            <button type="submit" class="flex flex-col items-center justify-center rounded-xl px-2 py-2 text-[10px] font-bold text-indigo-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mb-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1" /></svg>
                                Salir
                            </button>
                        </form>
                    @endif
                </div>
            </nav>
        @endauth
    </div>

    @livewireScripts
    @auth
        <script>
            (function() {
                var widget = document.querySelector('[data-alert-widget]');
                if (!widget) return;

                var toggle = widget.querySelector('[data-alert-toggle]');
                var panel = widget.querySelector('[data-alert-panel]');
                var list = widget.querySelector('[data-alert-list]');
                var count = widget.querySelector('[data-alert-count]');
                var readAll = widget.querySelector('[data-alert-read-all]');
                var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                function escapeHtml(value) {
                    return String(value || '').replace(/[&<>"']/g, function(char) {
                        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
                    });
                }

                function render(alerts) {
                    if (!alerts.length) {
                        list.innerHTML = '<div class="px-5 py-8 text-center text-sm font-semibold text-slate-500">No hay alertas recientes.</div>';
                        return;
                    }

                    list.innerHTML = alerts.map(function(alert) {
                        var dot = alert.read ? '' : '<span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-indigo-500"></span>';
                        var href = alert.actionUrl || '/alerts';
                        return '<a href="' + escapeHtml(href) + '" data-alert-link="' + escapeHtml(alert.id) + '" class="flex gap-3 border-b border-slate-100 px-5 py-4 last:border-b-0 hover:bg-slate-50">' +
                            dot +
                            '<span class="min-w-0 flex-1">' +
                                '<span class="block text-sm font-black text-slate-950">' + escapeHtml(alert.title) + '</span>' +
                                '<span class="mt-1 block text-xs font-medium leading-5 text-slate-600">' + escapeHtml(alert.body) + '</span>' +
                                '<span class="mt-2 block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">' + escapeHtml(alert.createdAt) + '</span>' +
                            '</span>' +
                        '</a>';
                    }).join('');
                }

                async function refreshAlerts() {
                    try {
                        var response = await fetch('{{ route('alerts.summary') }}', { headers: { 'Accept': 'application/json' } });
                        if (!response.ok) return;
                        var payload = await response.json();
                        var unread = Number(payload.unreadCount || 0);
                        count.textContent = unread > 99 ? '99+' : String(unread);
                        count.classList.toggle('hidden', unread <= 0);
                        render(payload.alerts || []);
                    } catch (error) {}
                }

                async function markRead(id) {
                    if (!id) return;
                    await fetch('/alerts/' + encodeURIComponent(id) + '/read', {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        }
                    }).catch(function() {});
                    refreshAlerts();
                }

                toggle.addEventListener('click', function(event) {
                    event.stopPropagation();
                    panel.classList.toggle('hidden');
                    refreshAlerts();
                });

                list.addEventListener('click', function(event) {
                    var link = event.target.closest('[data-alert-link]');
                    if (!link) return;
                    event.preventDefault();
                    markRead(link.dataset.alertLink).finally(function() {
                        window.location.href = link.getAttribute('href') || '/alerts';
                    });
                });

                readAll.addEventListener('click', function() {
                    fetch('{{ route('alerts.read-all') }}', {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        }
                    }).then(refreshAlerts).catch(function() {});
                });

                document.addEventListener('click', function(event) {
                    if (!widget.contains(event.target)) {
                        panel.classList.add('hidden');
                    }
                });

                refreshAlerts();
                setInterval(refreshAlerts, 8000);
            })();
        </script>
    @endauth
    @stack('scripts')
</body>
</html>


