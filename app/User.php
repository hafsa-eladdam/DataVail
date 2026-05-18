<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * C'EST ICI LA CLÉ DU SUCCÈS :
     * On autorise Laravel à remplir 'role' et 'status'.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',   // <--- INDISPENSABLE
        'status', // <--- INDISPENSABLE
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}