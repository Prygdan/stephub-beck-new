<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order\Cart;
use App\Http\Requests\Order\Update;
use App\Models\Order\CartItem;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    public function index() 
    {
        $orders = Cart::with([
            'guest', 
            'user', 
            'items.product',
            'items.size',
            'items.product.brand', 
            'items.product.images', 
            ])->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($orders, 200);
    }

    public function update(Update $request, $id)
    {
        $cart = Cart::with('guest', 'items')->findOrFail($id);
        $validated = $request->validated();

        // Оновлюємо дані гостя
        $cart->guest->update($validated);
    
        // Оновлюємо загальну ціну
        $cart->update([
            'comment'           => $validated['comment'], 
            'payment_method'    => $validated['payment_method'],
            'status'            => $validated['status']
        ]);
    
        return response()->json($cart, 200);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        // Спочатку видаляємо пов'язані елементи
        $cart->items()->delete();
        // Видаляємо гостя
        $cart->guest()->delete();
        // Видаляємо сам кошик
        $cart->delete();

        return response()->noContent(200);
    }
}
