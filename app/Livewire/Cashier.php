<?php 

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('Kasir')]
class Cashier extends Component
{
    use Toast;

    public ?string $selectedMethod = null;
    public bool $paid_type = false;

    public float $discount = 0;
    public string $discount_type = '%';
    public float $paid_amount = 0;

    public array $selectedProductId = [];
    public array $product_name = [];

    public $cart;

    public array $paid_methods = [
        ['name' => 'QRIS BCA'],
        ['name' => 'QRIS Gopay'],
        ['name' => 'BPJS'],
        ['name' => 'QRIS ALL'],
        ['name' => 'EDC BCA'],
        ['name' => 'EDC BRI'],
        ['name' => 'Goapotik'],
    ];

    public const DISCOUNT_TYPES = [
        ['id' => '%'],
        ['id' => 'Rp'],
    ];

    public function mount(CartService $cartService)
    {
        $this->loadCart($cartService);
    }

    public function loadCart(CartService $cartService)
    {
        $this->paid_type = false;
        $this->cart = $cartService->getUserCart(Auth::id());
    }

    public function updatedSelectedProductId(CartService $cartService)
    {
        $productId = end($this->selectedProductId);

        if ($productId) {
            $cartService->addToCart($productId);
            $this->selectedProductId = array_filter($this->selectedProductId, fn ($id) => $id !== $productId);
            $this->loadCart($cartService);
            $this->dispatch('cart-updated');
        }
    }

    public function updatedDiscount($value)
    {
        if ($this->discount_type === '%' && $value > 50) {
            $this->discount = 50;
            $this->error('Diskon tidak boleh lebih dari 50%!', timeout: 5000);
        }

        if ($value < 0) {
            $this->discount = 0;
            $this->error('Diskon tidak boleh negatif!', timeout: 5000);
        }
    }

    public function updatedPaidType()
    {
        $this->paid_amount = $this->roundUpToThousand($this->getCartSummary()['grandTotal']);
    }

    public function updatedSelectedMethod()
    {
        $this->paid_amount = $this->roundUpToThousand($this->getCartSummary()['grandTotal']);
    }

    public function getCartSummary(): array
    {
        return app(CartService::class)->getCartSummary($this->cart, $this->discount, $this->discount_type, $this->paid_amount);
    }

    public function updateOptionPrice($cartItemId, $basePrice, $option)
    {
        app(CartService::class)->updateOptionPrice($cartItemId, $basePrice, $option, $this->cart);
        $this->loadCart(app(CartService::class));
    }

    public function updatePriceAtTime($cartItemId, $price_at_time)
    {
        app(CartService::class)->updatePriceAtTime($cartItemId, $price_at_time);
        $this->loadCart(app(CartService::class));
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        app(CartService::class)->updateQuantity($cartItemId, $quantity, $this->cart);
        $this->loadCart(app(CartService::class));
    }

    public function delete($id)
    {
        app(CartService::class)->deleteItem($id);
        $this->toast('success', 'Data berhasil dihapus!');
    }

    public function checkout(CheckoutService $checkoutService)
    {
        $paid_method = ($this->paid_type === true) ? 'Uang Tunai' : $this->selectedMethod;
        if ($paid_method == "" || $paid_method === null) {
            $this->error('Pilih Metode Pembayaran!', timeout: 5000);
            return;
        }
        $item = $this->cart->items()->where('cart_id', $this->cart->id)->first();
        $summary = $this->getCartSummary();

        if ((int) $summary['grandTotal'] === 0 && $item === null) {
            $this->error('Keranjang kosong!', timeout: 5000);
            return;
        }

        if ($summary['paid_amount'] < $summary['grandTotal']) {
            $kurang = number_format($summary['grandTotal'] - $summary['paid_amount']);
            $this->error("Pembayaran tidak cukup!, kurang $kurang", timeout: 5000);
            return;
        }

        $checkoutService->process($this->cart, $summary, $this->discount, $this->discount_type, $paid_method);

        $this->reset(['discount', 'discount_type', 'paid_amount']);
        $this->loadCart(app(CartService::class));
        $this->success('Transaksi berhasil disimpan', timeout: 5000);
    }

    private function roundUpToThousand(int $value): int
    {
        return ceil($value / 1000) * 1000;
    }

    public function headers(): array
    {
        return [
            ['key' => 'index', 'label' => 'No.', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Produk', 'class' => 'w-1'],
            ['key' => 'quantity', 'label' => 'Kuantitas', 'class' => 'w-1'],
            ['key' => 'unit', 'label' => 'Satuan', 'class' => 'w-1'],
            ['key' => 'price', 'label' => 'Opsi Harga', 'class' => 'w-1'],
            ['key' => 'sell', 'label' => 'Harga Jual', 'class' => 'w-1'],
            ['key' => 'sub_total', 'label' => 'Sub Total', 'class' => 'w-1'],
            ['key' => 'action', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }

    public function render()
    {
        return view('livewire.cashier', [
            'products'     => Product::all()->toArray(),
            'headers'      => $this->headers(),
            'summary'      => $this->getCartSummary(),
            'paid_methods' => $this->paid_methods,
            'types'        => self::DISCOUNT_TYPES,
        ]);
    }
}