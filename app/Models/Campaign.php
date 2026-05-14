<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'Campaign';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'segmentFilter' => 'array',
        'sentAt' => 'datetime',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offerId', 'id');
    }

    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class, 'campaignId', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'campaignId', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdByUserId', 'id');
    }
}
