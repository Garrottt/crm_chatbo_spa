@extends('layouts.app')

@section('content')
<div class="min-h-screen overflow-hidden bg-slate-950">
    <div class="relative min-h-screen">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.22),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.18),_transparent_28%),linear-gradient(135deg,_#020617_0%,_#0f172a_40%,_#111827_100%)]"></div>
        <div class="absolute inset-y-0 left-0 hidden w-1/2 border-r border-white/5 bg-white/[0.03] lg:block"></div>

        <div class="relative z-10 grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
            <section class="hidden px-12 py-14 lg:flex lg:flex-col lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-4 rounded-full border border-white/10 bg-white/5 px-5 py-3 backdrop-blur">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-xl font-black text-indigo-700 shadow-lg">IK</div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-300">CRM Spa Ikigai</p>
                            <p class="mt-1 text-sm text-slate-300">Acceso interno del equipo</p>
                        </div>
                    </div>

                    <div class="mt-16 max-w-xl">
                        <p class="text-sm font-bold uppercase tracking-[0.38em] text-emerald-300/90">Panel profesional</p>
                        <h1 class="mt-5 text-5xl font-black leading-tight tracking-tight text-white">
                            Conversaciones, agenda y atención en un solo lugar.
                        </h1>
                        <p class="mt-6 text-lg leading-8 text-slate-300">
                            Ingresa al panel del spa para gestionar reservas, revisar interacciones con clientes y operar con una vista clara, privada y elegante.
                        </p>
                    </div>
                </div>

                <div class="grid max-w-2xl grid-cols-3 gap-4">
                    <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Agenda</p>
                        <p class="mt-3 text-2xl font-black text-white">Semanal</p>
                        <p class="mt-2 text-sm text-slate-300">Vista ordenada para reservas y disponibilidad.</p>
                    </div>

                    <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Chat</p>
                        <p class="mt-3 text-2xl font-black text-white">Centralizado</p>
                        <p class="mt-2 text-sm text-slate-300">Seguimiento de clientes y control del bot.</p>
                    </div>

                    <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Equipo</p>
                        <p class="mt-3 text-2xl font-black text-white">Seguro</p>
                        <p class="mt-2 text-sm text-slate-300">Accesos diferenciados por rol y operación.</p>
                    </div>
                </div>
            </section>

            <section class="flex items-center justify-center px-6 py-10 sm:px-8 lg:px-12">
                <div class="w-full max-w-xl">
                    <div class="rounded-[2rem] border border-white/10 bg-white/95 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
                        <div class="lg:hidden">
                            <div class="inline-flex items-center gap-3 rounded-full bg-slate-100 px-4 py-2">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-700 text-lg font-black text-white">IK</div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-indigo-700">Spa Ikigai</p>
                                    <p class="text-xs text-slate-500">Acceso interno</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 lg:mt-0">
                            <p class="text-sm font-bold uppercase tracking-[0.3em] text-indigo-600">Bienvenido</p>
                            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950">Iniciar sesión</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                Accede con tu cuenta para administrar reservas y conversaciones desde el panel del spa.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">
                                    Correo electrónico
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                    autofocus
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100 @error('email') border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100 @enderror"
                                    placeholder="correo@spaikigai.cl"
                                >
                                @error('email')
                                    <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">
                                    Contraseña
                                </label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100 @error('password') border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100 @enderror"
                                    placeholder="Ingresa tu contraseña"
                                >
                                @error('password')
                                    <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between gap-4 pt-1">
                                <label for="remember" class="inline-flex items-center gap-3 text-sm font-medium text-slate-600">
                                    <input
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        type="checkbox"
                                        name="remember"
                                        id="remember"
                                        {{ old('remember') ? 'checked' : '' }}
                                    >
                                    Mantener sesión iniciada
                                </label>
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-2xl bg-slate-950 px-5 py-4 text-sm font-bold uppercase tracking-[0.24em] text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                            >
                                Entrar al panel
                            </button>
                        </form>

                        <div class="mt-8 rounded-[1.5rem] bg-slate-50 px-5 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Acceso protegido</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Los administradores gestionan chats y agenda completa. Los especialistas acceden solo a las reservas asignadas a su agenda.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
