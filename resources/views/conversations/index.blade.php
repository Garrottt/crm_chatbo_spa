<x-app-layout>
    <div class="min-h-full overflow-y-auto bg-[linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)] px-6 py-6 md:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="rounded-[2rem] bg-slate-900 px-6 py-6 text-white shadow-2xl md:px-8">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-indigo-300">Conversaciones</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight md:text-4xl">Control operativo</h1>
                        <p class="mt-3 max-w-2xl text-sm text-slate-300 md:text-base">
                            Revisa conversaciones y detecta mensajes nuevos sin recargar la pagina.
                        </p>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-3 text-sm font-medium text-slate-200">
                        Actualizacion automatica cada 6 segundos
                    </div>
                </div>
            </section>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{{ session('error') }}</div>
            @endif

            <section id="conversation-grid" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @include('conversations._cards', ['conversations' => $conversations])
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const grid = document.getElementById('conversation-grid');
            const endpoint = @json(route('conversations.summaries'));
            const seenState = new Map();
            let fingerprint = null;

            const collectStateFromDom = () => {
                grid?.querySelectorAll('[data-conversation-id]').forEach((card) => {
                    seenState.set(card.dataset.conversationId, {
                        updatedAt: card.dataset.updatedAt || null,
                        messagesCount: Number(card.dataset.messagesCount || 0),
                    });
                });
            };

            const applyNewBadges = (items) => {
                items.forEach((item) => {
                    const previous = seenState.get(item.id);
                    const card = grid?.querySelector(`[data-conversation-id="${item.id}"]`);
                    const badge = card?.querySelector('.new-badge');

                    if (!card || !badge) {
                        return;
                    }

                    const hasNewActivity = previous
                        && item.messagesCount > previous.messagesCount
                        && item.updatedAt !== previous.updatedAt;

                    badge.classList.toggle('hidden', !hasNewActivity);
                    card.classList.toggle('ring-2', hasNewActivity);
                    card.classList.toggle('ring-indigo-300', hasNewActivity);
                    card.classList.toggle('shadow-indigo-100', hasNewActivity);

                    seenState.set(item.id, {
                        updatedAt: item.updatedAt,
                        messagesCount: item.messagesCount,
                    });
                });
            };

            collectStateFromDom();

            const refreshConversations = async () => {
                try {
                    const url = new URL(endpoint, window.location.origin);
                    if (fingerprint) {
                        url.searchParams.set('fingerprint', fingerprint);
                    }

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (response.status === 204) {
                        return;
                    }

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    if (!grid || !payload.html) {
                        return;
                    }

                    grid.innerHTML = payload.html;
                    fingerprint = payload.fingerprint || fingerprint;
                    applyNewBadges(payload.items || []);
                } catch (error) {
                    console.error('No se pudieron actualizar las conversaciones.', error);
                }
            };

            setInterval(refreshConversations, 6000);
        });
    </script>
</x-app-layout>
