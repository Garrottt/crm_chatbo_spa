<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'Conversation';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'collectedData' => 'array',
        'botPaused' => 'boolean',
        'bot_paused' => 'boolean',
        'taken_over_by_agent' => 'boolean',
        'taken_over_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversationId', 'id');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'conversationId', 'id')->latestOfMany('createdAt');
    }

    public function lastBooking()
    {
        return $this->belongsTo(Booking::class, 'lastBookingId', 'id');
    }

    public function takenOverByUser()
    {
        return $this->belongsTo(User::class, 'taken_over_by_user_id', 'id');
    }

    public function isBotPaused(): bool
    {
        return (bool) ($this->bot_paused || $this->botPaused);
    }

    public function isTakenOver(): bool
    {
        return (bool) $this->taken_over_by_agent;
    }
}
