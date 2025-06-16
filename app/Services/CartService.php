<?php 

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Faktur;
use App\Models\FakturItem;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getUserCart($userId): Cart
    {
        return Cart::with('items.product')->firstOrCreate(['user_id' => $userId]);
    }

    public function getFaktur($userId): Faktur
    {
        return Faktur::with('items.product')->firstOrCreate(['user_id' => $userId]);
    }

    public function getUserFaktur($userId): Faktur
    {
        return Faktur::with('items.product')->firstOrCreate(['user_id' => $userId, 'status' => 'draft']);
    }
    
    public function createFaktur($userId): Faktur
    {
        return Faktur::with('items.product')->create(['user_id' => $userId]);
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

    public function addToFaktur($productId, $fakturId)
    {
        // $faktur = $this->getUserFaktur(Auth::id());
        $faktur = Faktur::with('items.product')->findOrFail($fakturId);
        $item = $faktur->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->increment('quantity');
        } else {
            $product = Product::findOrFail($productId);
            $price = $product->purchase_price;

            $faktur->items()->create([
                'product_id' => $productId,
                'expired' => now()->format('Y-m-d'),
                'quantity' => 1,
                'price' => $price,
                'discount' => 0,
                'tax' => 11,
                'primary_price' => round($price * 1.11),
            ]);
        }

        // return $faktur;
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

    public function updatePriceFaktur($cartItemId, $price_at_time)
    {
        $item = FakturItem::findOrFail($cartItemId);
        // dd($item);
        FakturItem::findOrFail($cartItemId)->update(['price' => $price_at_time]);
        FakturItem::findOrFail($cartItemId)->update(['primary_price' => round(($price_at_time * (100-$item->discount)/100) * 1.11)]);
    }

    public function updateQuantity($cartItemId, $quantity, $cart)
    {
        $item = CartItem::with('product')->findOrFail($cartItemId);

        if ($item->cart_id !== $cart->id) abort(403);

        $stock = $item->product->stock;
        $quantity = max(1, min((int)$quantity, $stock));

        $item->update(['quantity' => $quantity]);
    }

    public function updateQtyFaktur($cartItemId, $quantity, $faktur)
    {
        $item = FakturItem::with('product')->findOrFail($cartItemId);

        if ($item->faktur_id !== $faktur->id) abort(403);

        $item->update(['quantity' => $quantity]);
    }

    public function deleteItem($id)
    {
        return CartItem::destroy($id);
    }

    public function deleteFaktur($id)
    {
        return FakturItem::destroy($id);
    }

    /*public function getFakturSummary(Faktur $faktur)
    {
        $subtotal = $faktur->items->sum(fn ($item) => round($item->quantity * $item->price * (100-$item->discount)/100));
        $ppnTotal = $faktur->items->sum(fn ($item) =>  round($item->quantity * $item->price) * 1.11);
        $inv = PurchaseInvoice::with('faktur')->find($faktur->id);
        if ($inv == null) {
            $discount = 0;
            $total = $faktur->items->sum(fn ($item) => $item->quantity * $item->primary_price * round(100 - $discount)/100);
            if ($discount === 0) {
                $ppn = round($total - $subtotal);
                
            } else {
                $ppn = round($total - ($subtotal * (100 - $discount)/100));
            }
            
        } else {
            $discount = $inv->discount;
            $total = $faktur->items->sum(fn ($item) => $item->quantity * $item->primary_price * round(100 - $inv->discount)/100);
            $ppn = round($total - ($subtotal * (100 - $inv->discount)/100));
        }
            $qty = $faktur->items->sum(fn ($item) => $item->quantity);

        return compact('ppn', 'total', 'subtotal', 'discount');
    }*/

    public function getFakturSummary(Faktur $faktur, int $discount_faktur = 0): array
{
    $subtotal = $faktur->items->sum(fn ($item) =>
        round($item->quantity * $item->price * (100 - $item->discount) / 100)
    );

    $qty = $faktur->items->sum(fn ($item) => $item->quantity);

    $total = $faktur->items->sum(fn ($item) =>
        round($item->quantity * $item->primary_price * (100 - $discount_faktur) / 100)
    );

    $ppn = round($total - ($subtotal * (100 - $discount_faktur) / 100));

    return compact('ppn', 'total', 'subtotal', 'discount_faktur');
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