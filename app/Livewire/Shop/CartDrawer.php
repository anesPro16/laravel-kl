<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\CartItem;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CartDrawer extends Component
{
    public $cart;

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = Cart::with('items.product')
            ->where('user_id', Auth::id())
            ->firstOrCreate(['user_id' => Auth::id()]);
    }

    /*public function increment($itemId)
    {
        $item = $this->cart->items()->find($itemId);
        $item->increment('quantity');
        $this->loadCart();
    }

    public function decrement($itemId)
    {
        $item = $this->cart->items()->find($itemId);
        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }
        $this->loadCart();
    }*/
    public function updateQuantity($cartItemId, $quantity)
{
    $quantity = max(1, (int) $quantity); // Mencegah quantity < 1

    $cartItem = CartItem::findOrFail($cartItemId);

    // Cek apakah item milik cart yang sedang aktif (opsional validasi)
    if ($cartItem->cart_id !== $this->cart->id) {
        abort(403);
    }

    $cartItem->update(['quantity' => $quantity]);
    $this->loadCart(); // Refresh cart
}

    public function render()
    {
        return view('livewire.shop.cart-drawer');
    }
}
