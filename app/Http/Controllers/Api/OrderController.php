<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Log; // Pour logger les erreurs

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $total += $product->price * $item['quantity'];
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'total' => $total,
            'status' => 'en cours',
        ]);

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
        }

        return response()->json(['message' => 'Commande passée avec succès']);
    }

    public function index(Request $request)
    {
        try {
            $orders = $request->user()->orders()->with('items.product')->get();
            return response()->json($orders);
        } catch (\Exception $e) {
            Log::error('Erreur dans /api/orders: '.$e->getMessage());
            return response()->json(['message' => 'Erreur serveur: '.$e->getMessage()], 500);
        }
    }
}
