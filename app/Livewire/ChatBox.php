<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\Conversation;
use App\Models\Message;

class ChatBox extends Component
{
    public $conversations;
    public $activeConversationId = null;
    public $botPaused = false;
    
    // 👇 Renombramos la variable aquí
    public $chatMessages = []; 
    public $newMessage = '';

    public function mount()
    {
        $this->conversations = Conversation::with('client')->orderBy('updatedAt', 'desc')->get();
    }

    public function loadConversation($id)
    {
        $this->activeConversationId = $id;
        $this->fetchMessages();
    }

    public function fetchMessages()
    {
        if ($this->activeConversationId) {
            // 👇 Y la renombramos aquí también
            $this->chatMessages = Message::where('conversationId', $this->activeConversationId)
                                     ->orderBy('createdAt', 'asc')
                                     ->get();
                                     
            $conv = Conversation::find($this->activeConversationId);
            if ($conv) {
                $this->botPaused = $conv->botPaused;
            }
            
            $this->conversations = Conversation::with('client')->orderBy('updatedAt', 'desc')->get();
        }
    }

    public function toggleBot()
    {
        if ($this->activeConversationId) {
            $conv = Conversation::find($this->activeConversationId);
            $conv->botPaused = !$conv->botPaused;
            $conv->save();

            $this->botPaused = $conv->botPaused;
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string'
        ]);

        $conversation = Conversation::with('client')->find($this->activeConversationId);

        $response = Http::post('http://localhost:3000/api/crm/send-message', [
            'whatsappNumber' => $conversation->client->whatsappNumber,
            'content' => $this->newMessage,
            'conversationId' => $conversation->id,
            'clientId' => $conversation->clientId,
        ]);

        if ($response->successful()) {
            $this->newMessage = ''; 
            $this->fetchMessages(); 
        } else {
            session()->flash('error', 'Error al enviar el mensaje por WhatsApp.');
        }
    }

    public function render()
    {
        return view('livewire.chat-box');
    }
}