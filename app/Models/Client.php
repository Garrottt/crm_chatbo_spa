<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // 1. Forzamos el nombre exacto de la tabla de Prisma
    protected $table = 'Client'; 

    // 2. Le indicamos a Laravel cómo se llaman las columnas de fecha en Prisma
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    // 3. Permitimos asignación masiva de campos
    protected $guarded = [];

    /*
     * IMPORTANTE: Si en Prisma configuraste el ID como String (UUID o CUID),
     * debes descomentar las siguientes dos líneas. Si es Autoincremental (Int), déjalas así.
     */
    public $incrementing = false;
    protected $keyType = 'string';

    // 4. Relaciones: Un cliente tiene muchas conversaciones
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
}
