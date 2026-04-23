<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // 1. Összes rendelés lekérdezése az Adminhoz
    public function index()
    {
        // Lekérjük a rendeléseket, a hozzájuk tartozó tételekkel és termékadatokkal együtt, a legújabbtól lefelé
        $orders = Order::with('items.product')->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }

    // 2. Új rendelés elmentése a Kosárból
    public function store(Request $request)
    {
        // Validáljuk a Vue-ból beérkező adatokat
        $request->validate([
            'cart' => 'required|array',
            'total_amount' => 'required|numeric',
            'shipping_address' => 'required|string'
        ]);

        // Létrehozzuk a fő rendelést a kapott címmel
        $order = Order::create([
            'total_amount' => $request->total_amount,
            'shipping_address' => $request->shipping_address, // Itt mentjük el a valós címet!
            'status' => 'pending'
        ]);

        // Végigmegyünk a kosár tartalmán, és lementjük a tételeket az order_items táblába
        foreach ($request->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        return response()->json(['message' => 'Sikeres rendelés', 'order' => $order], 201);
    }

    // 3. Státusz frissítése az Adminban (Függőben -> Kiszállítva, stb.)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,shipped,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Státusz sikeresen frissítve', 'order' => $order]);
    }
}
