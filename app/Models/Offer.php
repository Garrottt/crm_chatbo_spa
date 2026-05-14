<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'Offer';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'startsAt' => 'datetime',
        'endsAt' => 'datetime',
        'active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'serviceId', 'id');
    }

    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialistId', 'id');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'offerId', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'offerId', 'id');
    }
}
