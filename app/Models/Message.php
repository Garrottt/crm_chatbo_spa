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
        'mediaUrl'
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
}
