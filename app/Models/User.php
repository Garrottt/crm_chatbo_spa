<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'User';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function specialist()
    {
        return $this->hasOne(Specialist::class, 'userId', 'id');
    }

    public function takenOverConversations()
    {
        return $this->hasMany(Conversation::class, 'taken_over_by_user_id', 'id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isSpecialist(): bool
    {
        return $this->role === 'SPECIALIST';
    }

    public function getRememberTokenName()
    {
        return null;
    }
}
