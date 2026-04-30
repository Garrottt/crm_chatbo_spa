<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRM Spa Ikigai</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-100">

    <div class="flex h-screen overflow-hidden">
        <div class="w-16 flex flex-col items-center py-6 bg-indigo-900 text-white shadow-xl z-20">
            <div class="mb-8 p-2 bg-white rounded-lg">
                <span class="text-indigo-900 font-black text-xl">IK</span>
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

        <main class="flex-1 flex flex-col overflow-hidden">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
