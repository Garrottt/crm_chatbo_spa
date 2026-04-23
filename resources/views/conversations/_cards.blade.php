@foreach($conversations as $conversation)
    @php
        $isPaused = $conversation->bot_paused || $conversation->botPaused;
        $isTaken = $conversation->taken_over_by_agent;
    @endphp
    <article
        class="conversation-card rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-slate-200/60 backdrop-blur transition"
        data-conversation-id="{{ $conversation->id }}"
        data-updated-at="{{ optional($conversation->updatedAt)->toIso8601String() }}"
        data-messages-count="{{ $conversation->messages_count }}"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <p class="text-lg font-black text-slate-900">{{ $conversation->client->name ?? $conversation->client->whatsappNumber }}</p>
                    <span class="new-badge hidden rounded-full bg-indigo-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-700">
                        Nuevo
                    </span>
                </div>
                <p class="mt-1 text-sm font-medium text-slate-500">{{ $conversation->currentIntent }} / {{ $conversation->currentStep }}</p>
            </div>
            <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] {{ $isTaken ? 'bg-amber-100 text-amber-700' : ($isPaused ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700') }}">
                {{ $isTaken ? 'Tomada' : ($isPaused ? 'Pausada' : 'Activa') }}
            </span>
        </div>

        <div class="mt-5 space-y-3 text-sm">
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Mensajes</p>
                <p class="mt-1 font-medium text-slate-700">{{ $conversation->messages_count }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Control humano</p>
                <p class="mt-1 font-medium text-slate-700">{{ $conversation->takenOverByUser->name ?? 'Sin agente asignado' }}</p>
            </div>
        </div>

        <div class="mt-5">
            <a href="{{ route('conversations.show', $conversation) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-indigo-700">
                Ver conversacion
            </a>
        </div>
    </article>
@endforeach
