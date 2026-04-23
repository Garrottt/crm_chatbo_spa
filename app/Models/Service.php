<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'Service';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'serviceId', 'id');
    }

    public function specialists()
    {
        return $this->belongsToMany(Specialist::class, '_SpecialistServices', 'A', 'B', 'id', 'id');
    }
}
