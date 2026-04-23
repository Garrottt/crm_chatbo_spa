<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Conversation;
use App\Models\Message;

class ChatController extends Controller
{
    // 1. Mostrar la bandeja de entrada
    public function index()
    {
        // Traemos las conversaciones con sus clientes y últimos mensajes
        $conversations = Conversation::with('client')
            ->orderBy('updatedAt', 'desc')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    // 2. Mostrar una conversación específica
    public function show($id)
    {
        $conversation = Conversation::with(['client', 'messages' => function($query) {
            $query->orderBy('createdAt', 'asc');
        }])->findOrFail($id);

        return view('chat.show', compact('conversation'));
    }

    // 3. Tomar el control y enviar el mensaje mediante Node.js
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'required|string',
            'whatsappNumber' => 'required|string',
            'clientId' => 'required'
        ]);

        // A. (Opcional) Aquí actualizarías el estado de la conversación en BD
        // para decirle al bot de Node.js que se calle (ej: bot_paused = true)
        // Conversation::where('id', $conversationId)->update(['bot_paused' => true]);

        // B. Enviar la petición HTTP a tu servidor Node.js
        $response = Http::post('http://localhost:3000/api/crm/send-message', [
            'whatsappNumber' => $request->whatsappNumber,
            'content' => $request->content,
            'conversationId' => $conversationId,
            'clientId' => $request->clientId,
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Mensaje enviado correctamente.');
        }

        return back()->with('error', 'Error al comunicar con el motor de WhatsApp.');
    }
}