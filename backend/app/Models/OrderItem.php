<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Tömegesen kitölthető mezők.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    /**
     * Kapcsolat a termékkel (Minden tétel egy konkrét termékre mutat).
     * Ez teszi lehetővé a terméknév megjelenítését a rendelés részleteinél.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Visszakapcsolás a fő rendeléshez.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
