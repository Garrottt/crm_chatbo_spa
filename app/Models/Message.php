<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Mantiene la compatibilidad exacta con Prisma
    protected $table = 'Message';
    
    // Le decimos a Laravel que use createdAt, pero que IGNORE updatedAt
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null; 

    // Configuración para IDs tipo cuid() / UUID
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Lista exacta de lo que Laravel puede guardar (¡Ahora con 'sender' incluido!)
    protected $fillable = [
        'conversationId', 
        'clientId', 
        'direction', 
        'sender', 
        'messageType', 
        'content', 
        'media_url'
    ];

    // Convierte el JSON de Prisma en un Array de PHP automáticamente
    protected $casts = [
        'metadata' => 'array', 
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversationId', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId', 'id');
    }

    public function normalizedMessageType(): string
    {
        return strtolower((string) ($this->messageType ?? ($this->metadata['type'] ?? 'text')));
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->attributes['media_url'] ?? null;
    }

    public function hasMedia(): bool
    {
        return filled($this->mediaUrl);
    }

    public function mediaMetadata(): ?array
    {
        $media = $this->metadata['media'] ?? null;

        return is_array($media) ? $media : null;
    }

    public function hasMediaReference(): bool
    {
        return $this->hasMedia() || filled(data_get($this->mediaMetadata(), 'id'));
    }

    public function isImage(): bool
    {
        $type = $this->normalizedMessageType();
        $mediaUrl = strtolower((string) $this->mediaUrl);
        $mimeType = strtolower((string) data_get($this->mediaMetadata(), 'mimeType'));

        return str_starts_with($type, 'image')
            || str_starts_with($mimeType, 'image/')
            || preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?.*)?$/i', $mediaUrl) === 1;
    }
}
