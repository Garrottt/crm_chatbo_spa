<x-app-layout>
    <div class="flex-1 overflow-y-auto bg-slate-100 px-6 py-6">
        <div class="mx-auto max-w-4xl space-y-6">
            <div class="rounded-[28px] bg-slate-950 px-8 py-7 text-white shadow-xl">
                <p class="text-[11px] font-black uppercase tracking-[0.28em] text-indigo-300">Ofertas</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight">Editar oferta</h1>
                <p class="mt-2 text-sm text-slate-300">Ajuste la propuesta comercial sin perder el historial de uso de la oferta.</p>
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                @include('offers._form', [
                    'action' => route('offers.update', $offer),
                    'method' => 'PATCH',
                    'submitLabel' => 'Guardar cambios',
                ])
            </div>
        </div>
    </div>
</x-app-layout>
