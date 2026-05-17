<x-app-layout>
    <div class="h-full overflow-y-auto bg-slate-100 px-6 py-6 md:px-8">
        <div class="mx-auto max-w-5xl space-y-5">
            <section class="rounded-[2rem] bg-slate-950 px-8 py-7 text-white shadow-xl shadow-slate-300/40">
                <p class="text-xs font-black uppercase tracking-[0.35em] text-indigo-300">Alertas</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight">Centro de notificaciones</h1>
                <p class="mt-2 max-w-2xl text-sm font-medium text-slate-300">
                    Revise reservas confirmadas, cancelaciones y solicitudes de atencion humana.
                </p>
            </section>

            <form method="POST" action="{{ route('alerts.read-all') }}" class="flex justify-end">
                @csrf
                @method('PATCH')
                <button type="submit" class="rounded-2xl bg-white px-5 py-3 text-xs font-black uppercase tracking-[0.22em] text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50">
                    Marcar todas como leidas
                </button>
            </form>

            <section class="overflow-hidden rounded-[2rem] bg-white shadow-xl shadow-slate-200/70 ring-1 ring-slate-200">
                @forelse($alerts as $alert)
                    <article class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 last:border-b-0 md:flex-row md:items-center md:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                @unless($alert->readAt)
                                    <span class="h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                                @endunless
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                                    {{ str_replace('_', ' ', $alert->type) }}
                                </span>
                                <span class="text-xs font-bold text-slate-400">
                                    {{ optional($alert->createdAt)->timezone('America/Santiago')->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <h2 class="mt-2 text-lg font-black text-slate-950">{{ $alert->title }}</h2>
                            <p class="mt-1 text-sm font-medium text-slate-600">{{ $alert->body }}</p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            @if($alert->actionUrl)
                                <a href="{{ $alert->actionUrl }}" class="rounded-2xl bg-slate-950 px-4 py-2 text-xs font-black uppercase tracking-[0.2em] text-white transition hover:bg-indigo-700">
                                    Abrir
                                </a>
                            @endif
                            @if(!$alert->readAt)
                                <form method="POST" action="{{ route('alerts.read', $alert) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-black uppercase tracking-[0.2em] text-slate-600 transition hover:bg-slate-50">
                                        Leida
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-16 text-center text-sm font-semibold text-slate-500">
                        No hay alertas registradas.
                    </div>
                @endforelse
            </section>

            {{ $alerts->links() }}
        </div>
    </div>
</x-app-layout>
