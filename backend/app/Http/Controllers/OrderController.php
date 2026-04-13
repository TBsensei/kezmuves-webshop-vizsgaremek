<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'total_amount' => 'required|integer'
        ]);

        $order = Order::create([
            'user_id' => $request->user('sanctum') ? $request->user('sanctum')->id : null,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            'shipping_address' => 'Fizetésnél megadott cím'
        ]);

        foreach ($request->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        return response()->json(['message' => 'Sikeres rendelés!'], 201);
    }
}
