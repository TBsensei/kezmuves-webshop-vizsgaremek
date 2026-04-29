<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Admin: Összes rendelés listázása tételekkel együtt.
     */
    public function index(): JsonResponse
    {
        try {
            $orders = Order::with(['items.product', 'user:id,name,email'])
                ->latest()
                ->get();

            return response()->json($orders, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Hiba a rendelések lekérésekor.'], 500);
        }
    }

    /**
     * Vásárló: Új rendelés rögzítése (Tranzakció kezeléssel).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'shipping_address' => 'required|string|max:500'
        ]);

        // Tranzakció indítása: mindent vagy semmit
        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(), // A Sanctum middleware miatt egyszerűen elérhető
                'total_amount' => $request->total_amount,
                'shipping_address' => $request->shipping_address,
                'status' => 'pending'
            ]);

            foreach ($request->cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price']
                ]);
            }

            DB::commit(); // Ha idáig eljutott a kód, véglegesítjük a mentést

            return response()->json([
                'success' => true,
                'message' => 'Rendelésedet sikeresen rögzítettük!',
                'order_id' => $order->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Hiba esetén mindent visszavonunk, mintha meg sem történt volna
            Log::error("Rendelés mentési hiba: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a rendelés feldolgozása során.'
            ], 500);
        }
    }

    /**
     * Admin: Rendelés státuszának módosítása.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,shipped,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);

        try {
            $order->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Státusz sikeresen frissítve.',
                'status'  => $request->status
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Hiba történt a módosításkor.'], 500);
        }
    }

    /**
     * Vásárló: Saját rendeléstörténet lekérése.
     */
    public function myOrders(Request $request): JsonResponse
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($orders, 200);
    }
}
