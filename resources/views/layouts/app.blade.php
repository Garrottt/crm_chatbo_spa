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
    @auth
        <div class="flex h-screen overflow-hidden">
            <div class="group w-20 hover:w-64 transition-all duration-300 ease-in-out flex flex-col py-6 bg-indigo-900 text-white shadow-xl z-50">
                <div class="mb-8 mx-2 px-3 flex items-center">
                    <div class="p-2 bg-white rounded-lg flex-shrink-0 flex items-center justify-center">
                        <span class="text-indigo-900 font-black text-xl">IK</span>
                    </div>
                    <span class="font-bold text-2xl tracking-widest overflow-hidden whitespace-nowrap max-w-0 opacity-0 group-hover:max-w-xs group-hover:opacity-100 transition-all duration-300 ml-0 group-hover:ml-4">
                        IKIGAI
                    </span>
                </div>

                @if(auth()->user()?->isAdmin())
                    <a href="/chat" 
                       class="flex items-center px-3 py-3 mx-2 mb-4 rounded-xl transition-all duration-300 {{ request()->is('chat*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="font-semibold text-lg overflow-hidden whitespace-nowrap max-w-0 opacity-0 group-hover:max-w-xs group-hover:opacity-100 transition-all duration-300 ml-0 group-hover:ml-4">
                            Chats
                        </span>
                    </a>
                @endif

                <a href="/agenda" 
                   class="flex items-center px-3 py-3 mx-2 mb-4 rounded-xl transition-all duration-300 {{ request()->is('agenda*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-semibold text-lg overflow-hidden whitespace-nowrap max-w-0 opacity-0 group-hover:max-w-xs group-hover:opacity-100 transition-all duration-300 ml-0 group-hover:ml-4">
                        Agenda
                    </span>
                </a>

                <a href="/dashboard" 
                   class="flex items-center px-3 py-3 mx-2 mb-4 rounded-xl transition-all duration-300 {{ request()->is('dashboard*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="font-semibold text-lg overflow-hidden whitespace-nowrap max-w-0 opacity-0 group-hover:max-w-xs group-hover:opacity-100 transition-all duration-300 ml-0 group-hover:ml-4">
                        Estadísticas
                    </span>
                </a>

                <a href="/servicios" 
                   class="flex items-center px-3 py-3 mx-2 mb-4 rounded-xl transition-all duration-300 {{ request()->is('servicios*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-semibold text-lg overflow-hidden whitespace-nowrap max-w-0 opacity-0 group-hover:max-w-xs group-hover:opacity-100 transition-all duration-300 ml-0 group-hover:ml-4">
                        Servicios
                    </span>
                </a>

                <a href="/especialistas" 
                   class="flex items-center px-3 py-3 mx-2 mb-4 rounded-xl transition-all duration-300 {{ request()->is('especialistas*') ? 'bg-indigo-700 shadow-lg' : 'text-indigo-300 hover:bg-indigo-700 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="font-semibold text-lg overflow-hidden whitespace-nowrap max-w-0 opacity-0 group-hover:max-w-xs group-hover:opacity-100 transition-all duration-300 ml-0 group-hover:ml-4">
                        Especialistas
                    </span>
                </a>

                <div class="mt-auto mb-4 w-full flex flex-col">
                    <button class="flex items-center px-3 py-3 mx-2 text-indigo-400 hover:text-white hover:bg-indigo-700 rounded-xl transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-semibold text-lg overflow-hidden whitespace-nowrap max-w-0 opacity-0 group-hover:max-w-xs group-hover:opacity-100 transition-all duration-300 ml-0 group-hover:ml-4">
                            Cerrar sesión
                        </span>
                    </button>
                </div>
            </div>

            <main class="flex-1 flex flex-col overflow-hidden">
                @isset($slot)
                    {{ $slot }}
                @endisset

                @yield('content')
            </main>
        </div>
    @else
        @yield('content')
    @endauth

    @livewireScripts
    @stack('scripts') </body>
</html>
