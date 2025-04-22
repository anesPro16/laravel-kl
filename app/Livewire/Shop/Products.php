<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Products extends Component
{
    public $search = '';

    public function addToCart($productId)
    {
        $userId = Auth::id();

        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
        ]);

        $item = $cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->increment('quantity');
        } else {
        		$product = Product::findOrFail($productId);
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => 1,
                'price_at_time' => $product->selling_price,
            ]);
        }

        // Optional: emit event to CartDrawer to refresh
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn($q) => $q->where('product_name', 'like', "%{$this->search}%"))
            ->get();

        return view('livewire.shop.products', compact('products'));
    }
}
