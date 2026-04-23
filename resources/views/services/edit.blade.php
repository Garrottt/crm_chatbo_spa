<x-app-layout>
    <div class="min-h-full overflow-y-auto bg-[linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] px-6 py-6 md:px-8">
        <div class="mx-auto max-w-5xl space-y-6">
            <section class="rounded-[2rem] bg-slate-900 px-6 py-6 text-white shadow-2xl md:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-300">Catálogo</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight md:text-4xl">Editar servicio</h1>
            </section>

            <section class="rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur">
                @include('services._form', [
                    'action' => route('services.update', $service),
                    'method' => 'PATCH',
                    'submitLabel' => 'Guardar cambios',
                ])
            </section>
        </div>
    </div>
</x-app-layout>
