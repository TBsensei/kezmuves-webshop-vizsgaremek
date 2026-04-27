<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Engedélyezzük ezeknek a mezőknek a tömeges mentését
    protected $fillable = [
        'name',
        'description',
        'price',
        'category', // A kategória engedélyezése
        'image_url'
    ];

    // Kapcsolat: Egy termék több rendelési tételben (kosárban) is szerepelhet
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
