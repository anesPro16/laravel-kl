<?php 

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    public function addToCart($product, $qty = 1)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $qty;
            $cart[$product->id]['subtotal'] = $cart[$product->id]['quantity'] * $cart[$product->id]['price'];
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'price' => $product->selling_price,
                'quantity' => $qty,
                'subtotal' => $product->selling_price * $qty,
            ];
        }

        Session::put('cart', $cart);
    }

    public function getCart()
    {
        return Session::get('cart', []);
    }

    public function removeFromCart($productId)
    {
        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::put('cart', $cart);
    }

    public function updateQty($productId, $qty)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $qty;
            $cart[$productId]['subtotal'] = $qty * $cart[$productId]['price'];
            Session::put('cart', $cart);
        }
    }

    public function clearCart()
    {
        Session::forget('cart');
    }
}
