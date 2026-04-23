<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Engedélyezzük az adatbázisba való tömeges mentést ezekre az oszlopokra
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'shipping_address' // Ezen múlt a hiba, ezt engedélyezni kellett!
    ];

    // Kapcsolat az order_items táblával
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
