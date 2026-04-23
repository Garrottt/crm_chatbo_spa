@foreach($messages as $message)
    @php
        $isInbound = in_array(strtolower($message->direction ?? ''), ['inbound', 'incoming', 'in', 'received', 'entrada'], true)
            || strtolower($message->sender ?? '') === 'user';
    @endphp
    <div class="flex {{ $isInbound ? 'justify-start' : 'justify-end' }}">
        <div class="max-w-[78%] rounded-2xl px-4 py-3 shadow {{ $isInbound ? 'border border-slate-200 bg-white text-slate-800' : 'bg-indigo-600 text-white' }}">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] {{ $isInbound ? 'text-slate-400' : 'text-white/70' }}">
                {{ $isInbound ? 'Cliente' : 'Agente/Bot' }}
            </p>
            <p class="mt-2 whitespace-pre-line text-sm leading-6">{{ $message->content }}</p>
            <p class="mt-2 text-[11px] {{ $isInbound ? 'text-slate-400' : 'text-white/70' }}">
                {{ optional($message->createdAt)->format('d/m H:i') }}
            </p>
        </div>
    </div>
@endforeach
