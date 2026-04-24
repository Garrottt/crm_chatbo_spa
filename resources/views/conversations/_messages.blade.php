@foreach($messages as $message)
    @php
        $isInbound = in_array(strtolower($message->direction ?? ''), ['inbound', 'incoming', 'in', 'received', 'entrada'], true)
            || strtolower($message->sender ?? '') === 'user';
        $hasMedia = $message->hasMedia();
        $hasMediaReference = $message->hasMediaReference();
        $isImage = $message->isImage();
        $hasText = filled(trim((string) $message->content));
    @endphp
    <div class="flex {{ $isInbound ? 'justify-start' : 'justify-end' }}">
        <div class="max-w-[78%] rounded-2xl px-4 py-3 shadow {{ $isInbound ? 'border border-slate-200 bg-white text-slate-800' : 'bg-indigo-600 text-white' }}">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] {{ $isInbound ? 'text-slate-400' : 'text-white/70' }}">
                {{ $isInbound ? 'Cliente' : 'Agente/Bot' }}
            </p>

            @if($hasMedia && $isImage)
                <a href="{{ $message->mediaUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 block">
                    <img
                        src="{{ $message->mediaUrl }}"
                        alt="Imagen enviada en la conversacion"
                        loading="lazy"
                        decoding="async"
                        class="max-h-80 w-auto rounded-2xl border border-black/5 object-contain shadow-sm"
                    >
                </a>
            @elseif($hasMedia)
                <a
                    href="{{ $message->mediaUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-2 inline-flex items-center rounded-2xl border px-3 py-2 text-sm font-semibold {{ $isInbound ? 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100' : 'border-white/20 bg-white/10 text-white hover:bg-white/15' }}"
                >
                    Ver archivo adjunto
                </a>
            @elseif($hasMediaReference && $isImage)
                <div class="mt-2 rounded-2xl border border-dashed px-4 py-4 {{ $isInbound ? 'border-slate-300 bg-slate-50 text-slate-600' : 'border-white/25 bg-white/10 text-white/90' }}">
                    <p class="text-sm font-semibold">Imagen recibida</p>
                    <p class="mt-1 text-xs opacity-75">El CRM aun no tiene una URL descargable para mostrar esta imagen.</p>
                </div>
            @elseif($hasMediaReference)
                <div class="mt-2 rounded-2xl border border-dashed px-4 py-4 {{ $isInbound ? 'border-slate-300 bg-slate-50 text-slate-600' : 'border-white/25 bg-white/10 text-white/90' }}">
                    <p class="text-sm font-semibold">Adjunto recibido</p>
                    <p class="mt-1 text-xs opacity-75">El archivo existe, pero el CRM aun no tiene un enlace para abrirlo.</p>
                </div>
            @endif

            @if($hasText)
                <p class="mt-2 whitespace-pre-line text-sm leading-6">{{ $message->content }}</p>
            @elseif(!$hasMediaReference)
                <p class="mt-2 text-sm italic opacity-70">Mensaje sin contenido visible.</p>
            @endif

            <p class="mt-2 text-[11px] {{ $isInbound ? 'text-slate-400' : 'text-white/70' }}">
                {{ optional($message->createdAt)->format('d/m H:i') }}
            </p>
        </div>
    </div>
@endforeach
