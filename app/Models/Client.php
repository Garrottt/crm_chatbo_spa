<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'Client';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'marketingOptOut' => 'boolean',
        'marketingOptOutAt' => 'datetime',
        'lastInteractionAt' => 'datetime',
        'lastBookingAt' => 'datetime',
        'firstBookingAt' => 'datetime',
    ];

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'clientId');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'clientId', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'clientId', 'id');
    }

    public function campaignRecipients()
    {
        return $this->hasMany(CampaignRecipient::class, 'clientId', 'id');
    }
}
