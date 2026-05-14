<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignRecipient extends Model
{
    protected $table = 'CampaignRecipient';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'sentAt' => 'datetime',
        'respondedAt' => 'datetime',
        'bookedAt' => 'datetime',
        'deliveredAt' => 'datetime',
        'readAt' => 'datetime',
        'failedAt' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaignId', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId', 'id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingId', 'id');
    }
}
