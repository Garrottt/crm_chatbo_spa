<div class="flex h-full bg-gray-100" wire:poll.5s="fetchMessages">
    <div class="w-1/3 bg-white border-r overflow-y-auto">
        <div class="p-5 border-b bg-white">
            <h1 class="text-xl font-extrabold text-gray-800">Mensajes</h1>
            <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">Chatbot Spa Ikigai</p>
        </div>
        @foreach($conversations as $conv)
            <div wire:click="loadConversation('{{ $conv->id }}')" 
                 class="p-4 border-b cursor-pointer transition-colors duration-200 hover:bg-indigo-50 {{ $activeConversationId == $conv->id ? 'bg-indigo-50 border-l-4 border-indigo-600' : '' }}">
                <div class="flex justify-between items-center">
                    <p class="font-bold text-gray-800">{{ $conv->client->name ?? $conv->client->whatsappNumber }}</p>
                    <span class="text-[10px] text-gray-400">{{ $conv->updatedAt->diffForHumans(null, true) }}</span>
                </div>
                <p class="text-sm text-gray-500 truncate mt-1">
                    <span class="px-2 py-0.5 bg-gray-100 rounded text-[10px] font-bold text-gray-600 mr-2">{{ $conv->currentIntent }}</span>
                    {{ $conv->currentStep }}
                </p>
            </div>
        @endforeach
    </div>

    <div class="flex-1 flex flex-col shadow-2xl">
        @if($activeConversationId)
            <div class="p-4 bg-white border-b flex justify-between items-center shadow-sm z-10">
                <div>
                    <h2 class="font-bold text-lg text-gray-800">Chat con Cliente</h2>
                    <p class="text-xs text-green-500 flex items-center">
                        <span class="h-2 w-2 bg-green-500 rounded-full mr-2"></span> En línea
                    </p>
                </div>
                
                <button wire:click="toggleBot" 
                        class="px-5 py-2 rounded-full font-bold text-sm text-white shadow-md transition-all duration-300 {{ $botPaused ? 'bg-red-500 hover:bg-red-600 hover:scale-105' : 'bg-green-500 hover:bg-green-600 hover:scale-105' }}">
                    {{ $botPaused ? '🛑 BOT PAUSADO' : '⚡ BOT ACTIVADO' }}
                </button>
            </div>

            <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-[#f0f2f5] pattern-dots"> 
                @foreach($chatMessages as $msg)
                    @php
                        $dir = strtolower(trim($msg->direction ?? ''));
                        $sender = strtolower(trim($msg->sender ?? ''));
                        $isInbound = in_array($dir, ['inbound', 'incoming', 'in', 'received', 'entrada']) || $sender === 'user';
                        $isOutbound = !$isInbound;
                        $isAgent = ($isOutbound && in_array($sender, ['agent', 'agente', 'admin', 'system'])); 
                        $isBot = ($isOutbound && !$isAgent);
                        $hasMedia = $msg->hasMedia();
                        $hasMediaReference = $msg->hasMediaReference();
                        $isImage = $msg->isImage();
                        $mediaUrl = $msg->mediaUrl ?? null;
                        $hasText = filled(trim((string) $msg->content));
                    @endphp

                    <div class="flex {{ $isInbound ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[70%] px-4 py-3 shadow-md relative 
                            {{ $isInbound ? 'bg-white text-gray-800 rounded-2xl rounded-tl-none' : '' }}
                            {{ $isBot ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none' : '' }}
                            {{ $isAgent ? 'bg-emerald-500 text-white rounded-2xl rounded-tr-none shadow-lg' : '' }}
                        ">
                            <p class="text-[9px] mb-1 font-black uppercase tracking-tighter {{ $isInbound ? 'text-gray-400' : 'text-white/70' }}">
                                {{ $isBot ? '🤖 IA Ikigai' : ($isAgent ? '🧑‍💼 Agente' : '👤 Cliente') }}
                            </p>
                            
                            @if($hasMedia && $isImage)
                                <a href="{{ $mediaUrl }}" target="_blank" rel="noopener noreferrer" class="mb-2 block">
                                    <img
                                        src="{{ $mediaUrl }}"
                                        alt="Imagen enviada en la conversacion"
                                        loading="lazy"
                                        decoding="async"
                                        class="max-h-80 max-w-full rounded-lg border-2 border-white/20 object-contain shadow-inner sm:max-w-[300px]"
                                    >
                                </a>
                            @elseif($hasMedia)
                                <a
                                    href="{{ $mediaUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mb-2 inline-flex items-center rounded-xl border border-white/20 bg-black/10 px-3 py-2 text-xs font-bold"
                                >
                                    Ver archivo adjunto
                                </a>
                            @elseif($hasMediaReference && $isImage)
                                <div class="mb-2 rounded-lg border border-dashed border-white/20 bg-black/10 px-3 py-3 text-xs">
                                    <p class="font-bold">Imagen recibida</p>
                                    <p class="mt-1 opacity-75">Aun no hay una URL disponible para visualizarla en el CRM.</p>
                                </div>
                            @elseif($hasMediaReference)
                                <div class="mb-2 rounded-lg border border-dashed border-white/20 bg-black/10 px-3 py-3 text-xs">
                                    <p class="font-bold">Adjunto recibido</p>
                                    <p class="mt-1 opacity-75">El archivo existe, pero el CRM aun no puede abrirlo.</p>
                                </div>
                            @endif

                            @if($hasText)
                                <p class="text-[13px] leading-relaxed font-medium">{{ $msg->content }}</p>
                            @elseif(!$hasMediaReference)
                                <p class="text-[12px] italic opacity-70">Mensaje sin contenido visible.</p>
                            @endif
                            
                            <span class="text-[9px] opacity-60 flex justify-end mt-1 font-bold">
                                {{ \Carbon\Carbon::parse($msg->createdAt)->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 bg-white border-t">
                <div class="flex space-x-3 bg-gray-50 p-2 rounded-2xl border border-gray-100">
                    <input wire:model="newMessage" type="text" 
                           placeholder="Escribe un mensaje como agente..." 
                           class="flex-1 bg-transparent border-none focus:ring-0 text-sm px-2"
                           wire:keydown.enter="sendMessage">
                    <button wire:click="sendMessage" 
                            class="bg-indigo-600 text-white p-2.5 rounded-xl hover:bg-indigo-700 transition shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </button>
                </div>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-gray-300 bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="font-bold text-lg text-gray-500">Bandeja de Entrada Spa</p>
                <p class="text-sm">Selecciona un cliente para gestionar su atención</p>
            </div>
        @endif
    </div>
</div>
