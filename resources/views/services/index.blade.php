<x-app-layout>
    <div class="min-h-full overflow-y-auto bg-[linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] px-6 py-6 md:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="rounded-[2rem] bg-slate-900 px-6 py-6 text-white shadow-2xl md:px-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-300">Catálogo</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight md:text-4xl">Servicios</h1>
                        <p class="mt-3 text-sm text-slate-300 md:text-base">Administra duración, precio, estado y configuración operativa de los servicios del spa.</p>
                    </div>

                    <a href="{{ route('services.create') }}" class="rounded-2xl bg-white px-5 py-3 text-sm font-bold uppercase tracking-[0.22em] text-slate-900 transition hover:bg-indigo-100">
                        Nuevo servicio
                    </a>
                </div>
            </section>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($services as $service)
                    <article class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-slate-200/60 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-lg font-black text-slate-900">{{ $service->name }}</p>
                                <p class="mt-1 text-sm font-medium text-slate-500">{{ $service->code }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] {{ $service->active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                {{ $service->active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <div class="mt-5 space-y-3 text-sm">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Duración</p>
                                <p class="mt-1 font-medium text-slate-700">{{ $service->durationMinutes }} min</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Precio</p>
                                <p class="mt-1 font-medium text-slate-700">{{ number_format($service->price, 0, ',', '.') }} {{ $service->currency }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Especialistas</p>
                                <p class="mt-1 font-medium text-slate-700">{{ $service->specialists_count }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Reservas</p>
                                <p class="mt-1 font-medium text-slate-700">{{ $service->bookings_count }}</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <a href="{{ route('services.edit', $service) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-indigo-700">
                                Editar servicio
                            </a>
                        </div>
                    </article>
                @endforeach
            </section>
        </div>
    </div>
</x-app-layout>
