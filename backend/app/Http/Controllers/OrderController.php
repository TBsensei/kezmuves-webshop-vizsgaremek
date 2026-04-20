<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        // 1. Ellenőrizzük, hogy a kapott státusz érvényes-e
        $request->validate([
            'status' => 'required|string|in:pending,shipped,completed,cancelled'
        ]);

        // 2. Megkeressük a rendelést
        $order = Order::findOrFail($id);

        // 3. Frissítjük és elmentjük
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Státusz sikeresen frissítve', 'order' => $order]);
    }

    public function index()
    {
        // Lekérjük az összes rendelést a legújabbtól kezdve, hozzácsatolva a tételeket és a termékek adatait
        $orders = Order::with('items.product')->orderBy('created_at', 'desc')->get();

        return response()->json($orders);
    }

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
