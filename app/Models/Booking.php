<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'Booking';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'scheduledAt' => 'datetime',
        'endAt' => 'datetime',
        'offerDiscountSnapshot' => 'array',
        'paymentProofMetadata' => 'array',
        'paymentProofValidation' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'serviceId', 'id');
    }

    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialistId', 'id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaignId', 'id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offerId', 'id');
    }
}
