<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Alert extends Model
{
    protected $table = 'Alert';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'metadata' => 'array',
        'readAt' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId', 'id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingId', 'id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversationId', 'id');
    }

    public function isUnread(): bool
    {
        return $this->readAt === null;
    }

    protected static function booted(): void
    {
        static::creating(function (Alert $alert) {
            if (!$alert->getKey()) {
                $alert->{$alert->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
