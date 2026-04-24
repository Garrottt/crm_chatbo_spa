<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendConversationMessageRequest;
use App\Models\Conversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();

        $conversations = $this->conversationListingQuery()->get();

        return view('conversations.index', compact('conversations'));
    }

    public function summaries(Request $request)
    {
        $this->ensureAdmin();

        $fingerprint = $this->conversationSummaryFingerprint();

        if ($request->query('fingerprint') === $fingerprint) {
            return response()->noContent();
        }

        $conversations = $this->conversationListingQuery()->get();

        return response()->json([
            'fingerprint' => $fingerprint,
            'html' => view('conversations._cards', compact('conversations'))->render(),
            'items' => $conversations->map(fn (Conversation $conversation) => [
                'id' => $conversation->id,
                'messagesCount' => $conversation->messages_count,
                'updatedAt' => optional($conversation->updatedAt)->toIso8601String(),
            ])->values(),
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $this->ensureAdmin();

        $conversation->load([
            'client',
            'messages',
            'lastBooking.service',
            'lastBooking.specialist',
            'takenOverByUser',
        ]);

        return view('conversations.show', compact('conversation'));
    }

    public function messages(Request $request, Conversation $conversation)
    {
        $this->ensureAdmin();

        $latestMessageId = $conversation->messages()
            ->latest('createdAt')
            ->value('id');

        if ($request->query('last_message_id') === $latestMessageId) {
            return response()->json([
                'hasChanges' => false,
                'lastMessageId' => $latestMessageId,
            ]);
        }

        $conversation->load([
            'messages' => fn ($query) => $query->orderBy('createdAt'),
        ]);

        return response()->json([
            'hasChanges' => true,
            'html' => view('conversations._messages', [
                'messages' => $conversation->messages,
            ])->render(),
            'count' => $conversation->messages->count(),
            'lastMessageId' => $conversation->messages->last()?->id,
        ]);
    }

    public function pause(Conversation $conversation): RedirectResponse
    {
        $this->ensureAdmin();

        $this->updateConversationState($conversation, [
            'botPaused' => true,
            'bot_paused' => true,
        ]);

        return back()->with('success', 'Bot pausado para esta conversacion.');
    }

    public function resume(Conversation $conversation): RedirectResponse
    {
        $this->ensureAdmin();

        $this->updateConversationState($conversation, [
            'botPaused' => false,
            'bot_paused' => false,
            'taken_over_by_agent' => false,
            'taken_over_at' => null,
            'taken_over_by_user_id' => null,
        ]);

        return back()->with('success', 'Bot reanudado para esta conversacion.');
    }

    public function takeOver(Conversation $conversation): RedirectResponse
    {
        $this->ensureAdmin();

        $this->updateConversationState($conversation, [
            'botPaused' => true,
            'bot_paused' => true,
            'taken_over_by_agent' => true,
            'taken_over_at' => now(),
            'taken_over_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Tomaste control manual de la conversacion.');
    }

    public function release(Conversation $conversation): RedirectResponse
    {
        $this->ensureAdmin();

        $this->updateConversationState($conversation, [
            'taken_over_by_agent' => false,
            'taken_over_at' => null,
            'taken_over_by_user_id' => null,
            'botPaused' => false,
            'bot_paused' => false,
        ]);

        return back()->with('success', 'La conversacion fue liberada y el bot puede responder otra vez.');
    }

    public function sendMessage(SendConversationMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        $this->ensureAdmin();

        $conversation->load('client');

        if (! $conversation->client || empty($conversation->client->whatsappNumber)) {
            return back()->with('error', 'La conversacion no tiene un cliente con numero de WhatsApp disponible.');
        }

        try {
            $response = Http::connectTimeout(3)->timeout(10)->post($this->chatbotEndpoint('/api/crm/send-message'), [
                'whatsappNumber' => $conversation->client->whatsappNumber,
                'content' => $request->validated('content'),
                'conversationId' => $conversation->id,
                'clientId' => $conversation->clientId,
            ]);
        } catch (\Throwable $exception) {
            Log::error('No se pudo enviar el mensaje manual al chatbot.', [
                'conversation_id' => $conversation->id,
                'client_id' => $conversation->clientId,
                'chatbot_base_url' => config('services.chatbot.base_url'),
                'error' => $exception->getMessage(),
            ]);

            return back()->with('error', 'No se pudo conectar con el servicio del bot. Revisa CHATBOT_BASE_URL en Render.');
        }

        if (!$response->successful()) {
            return back()->with('error', 'No se pudo enviar el mensaje manual al cliente.');
        }

        $this->updateConversationState($conversation, [
            'botPaused' => true,
            'bot_paused' => true,
            'taken_over_by_agent' => true,
            'taken_over_at' => $conversation->taken_over_at ?: now(),
            'taken_over_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Mensaje manual enviado correctamente.');
    }

    private function updateConversationState(Conversation $conversation, array $attributes): void
    {
        $existingAttributes = [];

        foreach ($attributes as $column => $value) {
            if (Schema::hasColumn($conversation->getTable(), $column)) {
                $existingAttributes[$column] = $value;
            }
        }

        if (!empty($existingAttributes)) {
            $conversation->update($existingAttributes);
        }
    }

    private function conversationListingQuery()
    {
        return Conversation::with(['client', 'takenOverByUser', 'latestMessage'])
            ->withCount('messages')
            ->orderBy('updatedAt', 'desc');
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function chatbotEndpoint(string $path): string
    {
        return rtrim((string) config('services.chatbot.base_url'), '/') . $path;
    }

    private function conversationSummaryFingerprint(): string
    {
        $summary = Conversation::query()
            ->selectRaw('COUNT(*) as total, MAX("updatedAt") as latest_updated_at')
            ->first();

        return implode('|', [
            (string) ($summary?->total ?? 0),
            (string) ($summary?->latest_updated_at ?? ''),
        ]);
    }
}

