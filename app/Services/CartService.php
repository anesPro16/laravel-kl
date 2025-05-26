<?php 

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getUserCart($userId): Cart
    {
        return Cart::with('items.product')->firstOrCreate(['user_id' => $userId]);
    }

    public function addToCart($productId)
    {
        $cart = $this->getUserCart(Auth::id());
        $item = $cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->increment('quantity');
        } else {
            $product = Product::findOrFail($productId);
            $price = round($product->selling_price * 1.15);

            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => 1,
                'price_at_time' => $price,
            ]);
        }
    }

    public function updateOptionPrice($cartItemId, $basePrice, $option, $cart)
    {
        $cartItem = CartItem::with('product')->findOrFail($cartItemId);

        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        if ((int)$option === 4) {
            $cartItem->update(['option' => $option]);
            return;
        }

        $calculated = match ((int)$option) {
            1 => round($basePrice * 1.15),
            2 => round($basePrice * 1.3),
            3 => round($basePrice * 1.35),
            default => $basePrice,
        };

        $cartItem->update([
            'price_at_time' => $calculated,
            'option' => $option,
        ]);
    }

    public function updatePriceAtTime($cartItemId, $price_at_time)
    {
        CartItem::findOrFail($cartItemId)->update(['price_at_time' => $price_at_time]);
    }

    public function updateQuantity($cartItemId, $quantity, $cart)
    {
        $item = CartItem::with('product')->findOrFail($cartItemId);

        if ($item->cart_id !== $cart->id) abort(403);

        $stock = $item->product->stock;
        $quantity = max(1, min((int)$quantity, $stock));

        $item->update(['quantity' => $quantity]);
    }

    public function deleteItem($id)
    {
        return CartItem::destroy($id);
    }

    public function getCartSummary(Cart $cart, float $discount, string $discount_type, float $paid_amount): array
    {
        $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->price_at_time);

        if ($discount_type === '%' && $discount > 50) $discount = 50;

        $discountValue = $discount_type === '%'
            ? ($subtotal * $discount / 100)
            : $discount;

        $grandTotal = round($subtotal - $discountValue);
        $change = max(0, $paid_amount - $grandTotal);

        return compact('subtotal', 'discountValue', 'grandTotal', 'paid_amount', 'change');
    }
}