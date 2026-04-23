<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $table = 'Availability';

    public $timestamps = false;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialistId', 'id');
    }
}
