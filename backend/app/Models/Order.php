<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status', // pl. 'pending', 'completed', 'cancelled'
        'shipping_address'
    ];

    // Kapcsolat a felhasználóval (Egy rendelés egy felhasználóhoz tartozik)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
