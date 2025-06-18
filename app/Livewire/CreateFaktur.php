<?php

namespace App\Livewire;

use App\Livewire\Forms\PurchaseForm;
use App\Models\Faktur;
use App\Models\FakturItem;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;

#[Layout('components.layouts.empty')] 
#[Title('Faktur')]
class CreateFaktur extends Component
{
	use Toast;

    public PurchaseForm $form;

    public bool $showingForm = false;
    public bool $editing = false;
    // public string $expired = '';
    public float $discount =0;
    public int $discount_faktur = 0;
    public string $discount_type = '%';
    public float $grand_total = 0;

    public array $jenis_faktur = [
	    ['id' => 'Harga Belum termasuk Pajak'],
        ['id' => 'Harga Sudah termasuk Pajak'],
        ['id' => 'Tidak Dikenakan Pajak'],
	];

    public array $payments = [
        ['id' => 'Tunai'],
        ['id' => 'Kredit'],
    ];

    public array $selectedProductId = [];
    public array $product_name = [];

    public $faktur;

    public function mount(PurchaseInvoice $invoice = null, CartService $cartService)
    {
    	$this->discount_faktur = $invoice->discount ?? 0;
    	// $this->expired = now()->format('Y-m-d');
        $this->loadCart($cartService);
        
        /*$item = FakturItem::find($this->faktur->id);
        if ($item) {
        	$this->loadCart($cartService);
        } else {
        	$this->createFaktur($cartService);
        }*/

        $this->form->fillForm($invoice, $this->getFakturSummary());
        $this->resetValidation();
    }

    public function createFaktur(CartService $cartService)
    {
        $this->faktur = $cartService->createFaktur(Auth::id());
    }

    public function loadCart(CartService $cartService)
    {
        $this->faktur = $cartService->getUserFaktur(Auth::id());
    }

    public function cartHeaders(): array
    {
        return [
            ['key' => 'index', 'label' => 'No.', 'class' => 'w-1 text-black'],
            ['key' => 'name', 'label' => 'Produk', 'class' => 'w-1 text-black'],
            ['key' => 'expired', 'label' => 'Expired', 'class' => 'w-1 text-black'],
            ['key' => 'quantity', 'label' => 'Kuantitas', 'class' => 'w-1 text-black'],
            ['key' => 'unit', 'label' => 'Satuan', 'class' => 'w-1 text-black'],
            ['key' => 'sell', 'label' => 'Harga Beli', 'class' => 'w-1 text-black'],
            ['key' => 'discount', 'label' => 'Diskon', 'class' => 'w-1 text-black'],
            ['key' => 'tax', 'label' => 'Pajak', 'class' => 'w-1 text-black'],
            ['key' => 'primary_price', 'label' => 'Harga Pokok', 'class' => 'w-1 text-black'],
            ['key' => 'sub_total', 'label' => 'Sub Total', 'class' => 'w-1 text-black'],
            ['key' => 'action', 'label' => 'Aksi', 'class' => 'w-1 text-black'],
        ];
    }

    public function updatedSelectedProductId(CartService $cartService)
    {
        $productId = end($this->selectedProductId);

        if ($productId) {
            $cartService->addToFaktur($productId, $this->faktur->id);
            $this->selectedProductId = array_filter($this->selectedProductId, fn ($id) => $id !== $productId);
            $this->loadCart($cartService);
            $this->dispatch('cart-updated');
        }
        $this->loadCart($cartService);
    }

    public function updateExpired($fakturItemId, $expired)
    {
        // Validasi: tanggal expired tidak boleh di masa lalu
        if (Carbon::parse($expired)->lt(now()->startOfDay())) {
            // $this->addError("expired_{$fakturItemId}", '');
            $this->error('Waktu kadaluarsa sudah berlalu!', timeout: 5000);
            return;
        }
        app(CartService::class)->updateExpiredFaktur($fakturItemId, $expired, $this->faktur);
        $this->loadCart(app(CartService::class));
        $this->getFakturSummary();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        if ($quantity == null) {
            $this->error('Kuantitas kosong!', timeout: 5000);
            $quantity = 1;
            // return;
        }
        app(CartService::class)->updateQtyFaktur($cartItemId, $quantity, $this->faktur);
        $this->loadCart(app(CartService::class));
        $this->getFakturSummary();
    }

    public function updatePriceAtTime($cartItemId, $price_at_time)
    {
        app(CartService::class)->updatePriceFaktur($cartItemId, $price_at_time);
        $this->loadCart(app(CartService::class));
    }

    public function updateDiscountFaktur($value)
    {
    	/*$this->discount_faktur =  ($value) ? $value : 0 ;
    	$summary = $this->getFakturSummary();
    	$summary['discount'] = $this->discount_faktur;
    	$summary['total'] = round($summary['total'] * (100 - $this->discount_faktur)/100);*/
        $this->discount_faktur = is_numeric($value) ? (int) $value : 0;

        $this->form->discount = $this->discount_faktur;

        // Refresh data faktur agar getFakturSummary memperhitungkan diskon baru
        $this->dispatch('fakturDiscountUpdated');
    }

    public function updateDiscount($cartItemId, $value = 0)
    {
        $item = FakturItem::findOrFail($cartItemId);

        if ($item->faktur_id !== $this->faktur->id) abort(403);

        if ($this->discount_type === '%' && $value > 50) {
            $this->discount = 50;
            $item->update(['discount' => 50]);
            $this->error('Diskon tidak boleh lebih dari 50%!', timeout: 5000);
            return false;
        }

        if ($value < 0) {
            $this->discount = 0;
            $this->error('Diskon tidak boleh negatif!', timeout: 5000);
        }


        $item->update(['discount' => $value ?? 0]);
        $item->update(['primary_price' => round(($item->price * (100-$value)/100) * 1.11)]);
    }

    public function delete($id)
    {
        app(CartService::class)->deleteFaktur($id);
        $this->toast('success', 'Data berhasil dihapus!');
    }

    public function getFakturSummary(): array
    {
        if ($this->faktur) {
            return app(CartService::class)->getFakturSummary($this->faktur, $this->discount_faktur);
        }

        return ['subtotal' => 0, 'ppn' => 0, 'total' => 0, 'discount' => 0];
    }

    public function save(CartService $cartService)
    {
      $this->form->save($this->getFakturSummary(), $this->faktur->id);
  		// dd($this->faktur->id);
  		$getFaktur =  Faktur::with('items.product')->findOrFail($this->faktur->id);
  		foreach ($getFaktur->items as $item) {
				$product = Product::find($item->product_id);
				// dd((int)$product->stok);
				$product->update(['stock' => $product->stok + $item->quantity]);
				// $product?->increment('stok', $item->quantity);
				// dd($product);
			}
  		$getFaktur->update(['status' => 'process']);

      // $message = $this->form->record->id ? 'Data berhasil diupdate' : '';
      $this->reset();
      $this->redirect('/faktur', navigate: true);
      $this->success('Data berhasil dibuat', 'success!', timeout: 5000);
    }

    public function render()
    {
        return view('livewire.create-faktur', [
            'cartHeaders'  => $this->cartHeaders(),
            'inventories'  => Inventory::select('id', 'inventory_name')->get()->toArray(),
            'jenis_faktur' => $this->jenis_faktur,
            'payments'     => $this->payments,
            'products'     => Product::select('id', 'product_name', 'stock', 'stok')->get()->toArray(),
            'summary'      => $this->getFakturSummary(),
            'suppliers'    => Supplier::select('id', 'nama')->get()->toArray(),
        ]);
    }
}
