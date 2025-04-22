<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Unit;
use App\Services\CartService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;
#[Title('Kasir')]
class Cashier extends Component
{
	use Toast;

	public array $product_name = [];

	public $option_price = '';
  
  public $cart;

  public array $selectedProductId = [];

	/*public float $discount = 0;
	public string $discountType = 'nominal';
	public float $total = 0;
	public float $totalAfterDiscount = 0;*/
	public float $discount = 0;
	public string $discount_type = 'percent'; // 'percent' atau 'nominal'
	public float $paid_amount = 0;

	 // Table headers
    public function headers(): array
    {
      return [
          ['key' => 'index', 'label' => 'No.', 'class' => 'w-1'],
          ['key' => 'name', 'label' => 'Produk', 'class' => 'w-1'],
          ['key' => 'quantity', 'label' => 'kuantitas', 'class' => 'w-1'],
          ['key' => 'unit', 'label' => 'Satuan', 'class' => 'w-1'],
          ['key' => 'price', 'label' => 'Opsi Harga', 'class' => 'w-1'],
          ['key' => 'sell', 'label' => 'Harga Jual', 'class' => 'w-1'],
          ['key' => 'sub_total', 'label' => 'Sub Total', 'class' => 'w-1'],
          ['key' => 'action', 'label' => 'Aksi', 'class' => 'w-1'],
      ];
    }

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

    public function updatedSelectedProductId()
    {
        // Ambil elemen terakhir yang dipilih (opsi terbaru)
        $productId = end($this->selectedProductId);

        if ($productId) {
            $this->addToCart($productId);

            // Hapus dari array setelah diproses
            $this->selectedProductId = array_filter(
                $this->selectedProductId,
                fn ($id) => $id !== $productId
            );
        }
    }


public function getCartSummary(): array
{
    $subtotal = $this->cart->items->sum(fn ($item) => $item->quantity * $item->price_at_time);
    $value = (float) ($this->discount ?? 0);
    $valuePaidAmount = (float) ($this->paid_amount ?? 0);

    // Validasi diskon maksimal 50% jika tipe persen
    if ($this->discount_type === 'percent' && $value > 50) {
        $value = 50;
        $this->discount = 50; // juga update di UI agar tidak tetap 51++
    }

    $discountValue = $this->discount_type === 'percent'
        ? ($subtotal * $value / 100)
        : $value;

    $grandTotal = max(0, $subtotal - $discountValue);
    $change = max(0, $valuePaidAmount - $grandTotal);

    return [
        'subtotal'     => $subtotal,
        'discount'     => $discountValue,
        'grand_total'  => $grandTotal,
        'paid_amount'  => $valuePaidAmount,
        'change'       => $change,
    ];
}

public function updatedDiscount($value)
{
    // Reset error jika ada
    $this->resetErrorBag('discount');

    if ($this->discount_type === 'percent' && $value > 50) {
        // Batasi input discount maksimal 50%
        $this->discount = 50;
        $this->addError('discount', 'Diskon tidak boleh lebih dari 50%.');
    }

    if ($value < 0) {
        $this->discount = 0;
        $this->addError('discount', 'Diskon tidak boleh negatif.');
    }
}


    public function addToCart($productId)
    {
        $userId = Auth::id();
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

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

        $this->loadCart(); // Refresh cart
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($cartItemId, $quantity)
{
    $cartItem = CartItem::with('product')->findOrFail($cartItemId); // Pastikan relasi product tersedia

    // Validasi cart item milik cart yang aktif
    if ($cartItem->cart_id !== $this->cart->id) {
        abort(403);
    }

    // Ambil stok produk
    $stock = $cartItem->product->stock;

    // Cegah qty < 1 atau > stok produk
    if ($quantity > $stock) {
    	$quantity = $stock;
    	$cartItem->update(['quantity' => $quantity]);
    } else {
    	// $quantity = max(1, min((int) $quantity, $stock));
    	$quantity = max(1, (int) $quantity);    	
    }

    // Update quantity jika valid
    $cartItem->update(['quantity' => $quantity]);

    // $this->loadCart(); // Refresh data cart
}

public function checkout()
{
    $summary = $this->getCartSummary();

    if ($summary['paid_amount'] < $summary['grand_total']) {
        $this->addError('paid_amount', 'Pembayaran tidak cukup.');
        return;
    }

    DB::transaction(function () use ($summary) {
        // 1. Buat transaksi penjualan
        $sale = \App\Models\Sale::create([
            'user_id'       => auth()->id(),
            'subtotal'      => $summary['subtotal'],
            'discount'      => $this->discount,
            'discount_type' => $this->discount_type,
            'grand_total'   => $summary['grand_total'],
            'paid_amount'   => $summary['paid_amount'],
            'change'        => $summary['change'],
        ]);

        // 2. Simpan item transaksi dan kurangi stok
        foreach ($this->cart->items as $item) {
            // Buat item penjualan
            $sale->items()->create([
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->price_at_time,
                'subtotal'   => $item->quantity * $item->price_at_time,
            ]);

            // Kurangi stok produk
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->decrement('stock', $item->quantity);
            }
        }

        // 3. Kosongkan keranjang
        $this->cart->items()->delete();

        // 4. Reset form dan refresh cart
        $this->reset(['discount', 'discount_type', 'paid_amount']);
        $this->loadCart();
    });

    session()->flash('success', 'Transaksi berhasil disimpan!');
}



		// Delete action
  public function delete($id): void
  {
    if (CartItem::destroy($id)) {
        $this->toast('success', 'Data berhasil dihapus!');
    }
  }

  public function render()
  {
      return view('livewire.cashier', [
      	'products' => Product::all()->toArray(),
      	'headers' => $this->headers(),
      	'summary' => $this->getCartSummary(),
      ]);
  }
}
