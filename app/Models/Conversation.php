<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'Conversation';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    // Si tu ID es un string/UUID en Prisma, descomenta esto:
    public $incrementing = false;
    protected $keyType = 'string';

    // Le decimos a Laravel que el campo de datos adicionales (que vimos en tu JS) es un JSON/Array
    protected $casts = [
        'collectedData' => 'array',
        'botPaused' => 'boolean',
        'bot_paused' => 'boolean',
        'taken_over_by_agent' => 'boolean',
        'taken_over_at' => 'datetime',
    ];

    // Relaciones: Una conversación pertenece a un cliente
    public function client()
    {
        // El segundo parámetro es la clave foránea (como se llama en tu BD)
        // El tercer parámetro es la clave primaria del cliente
        return $this->belongsTo(Client::class, 'clientId', 'id');
    }

    // Relaciones: Una conversación tiene muchos mensajes
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversationId', 'id');
    }

    public function lastBooking()
    {
        return $this->belongsTo(Booking::class, 'lastBookingId', 'id');
    }

    public function takenOverByUser()
    {
        return $this->belongsTo(User::class, 'taken_over_by_user_id', 'id');
    }
}
