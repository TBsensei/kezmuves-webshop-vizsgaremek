<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * Az adatbázisban tömegesen módosítható mezők listája.
     */
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'shipping_address'
    ];

    /**
     * Kapcsolat a rendelési tételekkel (Egy rendeléshez több tétel tartozik).
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Kapcsolat a felhasználóval (Egy rendelés egy regisztrált felhasználóhoz tartozik).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
