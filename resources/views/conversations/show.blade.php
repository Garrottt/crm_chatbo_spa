<x-app-layout>
    <div class="min-h-full overflow-y-auto bg-[linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] px-6 py-6 md:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="rounded-[2rem] bg-slate-900 px-6 py-6 text-white shadow-2xl md:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-300">Conversacion</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight md:text-4xl">
                            {{ $conversation->client->name ?? $conversation->client->whatsappNumber }}
                        </h1>
                        <p class="mt-3 text-sm text-slate-300 md:text-base">
                            Estado actual: {{ $conversation->currentIntent }} / {{ $conversation->currentStep }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('conversations.pause', $conversation) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-2xl bg-rose-500 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Pausar bot</button>
                        </form>
                        <form method="POST" action="{{ route('conversations.resume', $conversation) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Reanudar bot</button>
                        </form>
                        <form method="POST" action="{{ route('conversations.take-over', $conversation) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-2xl bg-amber-500 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white">Tomar control</button>
                        </form>
                    </div>
                </div>
            </section>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{{ session('error') }}</div>
            @endif

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Mensajes</p>
                            <p class="mt-1 text-sm font-medium text-slate-600">La vista se actualiza automaticamente cada pocos segundos.</p>
                        </div>
                        <span id="messages-count" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-600">
                            {{ $conversation->messages->count() }} mensajes
                        </span>
                    </div>

                    <div id="messages-panel" class="max-h-[68vh] overflow-y-auto rounded-[1.75rem] bg-slate-50 px-4 py-4">
                        <div id="messages-list" class="space-y-4">
                            @include('conversations._messages', ['messages' => $conversation->messages->sortBy('createdAt')])
                        </div>
                    </div>

                    <form id="manual-message-form" method="POST" action="{{ route('conversations.messages.store', $conversation) }}" class="mt-6 border-t border-slate-200 pt-6">
                        @csrf
                        <label for="content" class="mb-2 block text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Intervenir manualmente</label>
                        <textarea id="content" name="content" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-900 outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-2 text-sm font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs font-medium text-slate-500">Al enviar, la conversacion queda bajo control humano.</p>
                            <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-indigo-700">Enviar mensaje</button>
                        </div>
                    </form>
                </div>

                <aside class="rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur">
                    <div class="space-y-4 text-sm">
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Bot pausado</p>
                            <p class="mt-1 font-medium text-slate-700">{{ ($conversation->bot_paused || $conversation->botPaused) ? 'Si' : 'No' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Tomada por agente</p>
                            <p class="mt-1 font-medium text-slate-700">{{ $conversation->taken_over_by_agent ? 'Si' : 'No' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Agente asignado</p>
                            <p class="mt-1 font-medium text-slate-700">{{ $conversation->takenOverByUser->name ?? 'Sin asignar' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Ultima reserva</p>
                            <p class="mt-1 font-medium text-slate-700">{{ $conversation->lastBooking?->service->name ?? 'Sin reserva asociada' }}</p>
                        </div>
                    </div>
                </aside>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const panel = document.getElementById('messages-panel');
            const list = document.getElementById('messages-list');
            const count = document.getElementById('messages-count');
            const form = document.getElementById('manual-message-form');
            const endpoint = @json(route('conversations.messages.index', $conversation));
            let lastMessageId = @json($conversation->messages->sortBy('createdAt')->last()?->id);

            const scrollToBottom = (smooth = false) => {
                if (!panel) {
                    return;
                }

                panel.scrollTo({
                    top: panel.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto',
                });
            };

            const isNearBottom = () => {
                if (!panel) {
                    return true;
                }

                return panel.scrollHeight - panel.scrollTop - panel.clientHeight < 140;
            };

            scrollToBottom();

            const refreshMessages = async () => {
                try {
                    const shouldStickToBottom = isNearBottom();
                    const url = new URL(endpoint, window.location.origin);
                    if (lastMessageId) {
                        url.searchParams.set('last_message_id', lastMessageId);
                    }

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();

                    if (payload.hasChanges && payload.lastMessageId && payload.lastMessageId !== lastMessageId) {
                        list.innerHTML = payload.html;
                        count.textContent = `${payload.count} mensajes`;
                        lastMessageId = payload.lastMessageId;

                        if (shouldStickToBottom) {
                            scrollToBottom(true);
                        }
                    }
                } catch (error) {
                    console.error('No se pudieron actualizar los mensajes.', error);
                }
            };

            setInterval(refreshMessages, 5000);

            form?.addEventListener('submit', () => {
                setTimeout(() => {
                    scrollToBottom(true);
                }, 150);
            });
        });
    </script>
</x-app-layout>
