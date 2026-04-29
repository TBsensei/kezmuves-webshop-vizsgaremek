<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Tömegesen módosítható mezők.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    /**
     * JSON válaszokból elrejtett mezők (biztonság).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Adat típuskonverziók (Casting).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Automatikus hashelés kezelése Laravel 10+ alatt
    ];

    /**
     * Kapcsolat: Egy felhasználóhoz több rendelés tartozhat.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Segédmetódus az adminisztrátori jogosultság ellenőrzéséhez.
     * Ez a "Clean Code" egyik alapköve a jogosultságkezelésben.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
