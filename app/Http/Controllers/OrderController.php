<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\FastStore;
use App\Http\Requests\Order\Store;
use App\Models\Order\Cart;
use App\Models\Order\CartItem;
use App\Models\Order\Guest;
use App\Models\Product;

class OrderController extends Controller
{
    public function store(Store $request) 
    {
        $order = $this->storeGuestCartData($request);

        return response()->json($order, 200);
    }

    public function fastStore(FastStore $request)
    {
        $order = $this->storeGuestCartData($request);

        return response()->json($order, 200);
    }

    protected function storeGuestCartData($request)
    {
        $validated = $request->validated();
        $guest = Guest::create($validated);
        $cart = Cart::create([
            'guest_id'          => $guest->id,
            'total_price'       => 0,
            'status'            => 2,
            'payment_method'    => $validated['payment_method'] ?? null,
        ]);

        $totalPrice = 0;

        foreach ($validated['products'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $unitPrice = $product->discounted_price
                ?? $product->price;

            CartItem::create([
                'cart_id'      => $cart->id,
                'product_id'   => $product->id,
                'size_id'      => (int) $item['size_id'] ?? null,
                'quantity'     => (int) $item['quantity'],
                'price'        => $unitPrice,
            ]);

            $totalPrice += $unitPrice * (int) $item['quantity'];
        }

        $cart->update([
            'total_price' => round($totalPrice, 2),
        ]);

        return $cart->load('items');
    }
}
