<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    protected $table = 'Specialist';

    public $timestamps = false;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'specialistId', 'id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, '_SpecialistServices', 'B', 'A', 'id', 'id');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class, 'specialistId', 'id')->orderBy('dayOfWeek');
    }
}
