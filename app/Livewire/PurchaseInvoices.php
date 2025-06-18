<?php

namespace App\Livewire;

use App\Exports\InvoiceExport;
use App\Livewire\Forms\PurchaseForm;
use App\Livewire\Traits\HasFakturList;
use App\Models\Faktur;
use App\Models\FakturItem;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

#[Title('Faktur')]
class PurchaseInvoices extends Component
{
    use HasFakturList, Toast;

    public $startDate, $endDate;

    public PurchaseForm $form;

    public $title = 'Daftar Pembelian';

    public $judul = '';

    public bool $showingForm = false;
    public bool $editing = false;
    public bool $readonly = false;

    public float $discount = 0;

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

    public function mount(CartService $cartService)
    {
        // $this->loadCart($cartService);
        // $this->grand_total = $this->getFakturSummary()['subtotal'] ?? 0;
    }

    public function loadCart(CartService $cartService)
    {
        $this->faktur = $cartService->getFaktur(Auth::id());
    }

    public function createFaktur(CartService $cartService)
    {
        $this->faktur = $cartService->createFaktur(Auth::id());
        $this->redirect('/create-faktur', navigate: true);
    }

    public function updatedSelectedProductId(CartService $cartService)
    {
        if ($this->readonly == true) {
            return;
        }
        $productId = end($this->selectedProductId);

        if ($productId) {
            $cartService->addToFaktur($productId, $this->faktur->id);
            $this->selectedProductId = array_filter($this->selectedProductId, fn ($id) => $id !== $productId);
            // $this->loadCart($cartService);
            $this->dispatch('cart-updated');
        }
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        app(CartService::class)->updateQtyFaktur($cartItemId, $quantity, $this->faktur);
        // $this->loadCart(app(CartService::class));
    }

    public function updatePriceAtTime($cartItemId, $price_at_time)
    {
        app(CartService::class)->updatePriceFaktur($cartItemId, $price_at_time);
        // $this->loadCart(app(CartService::class));
    }

    public function updateDiscountFaktur($value)
    {
        /*$inv = PurchaseInvoice::with('faktur')->findOrFail($this->faktur->id);
        if ($value == null) {
            $value = 0;
            $this->discount_faktur = $value;
        }
        $inv->update(['discount' => $value]);*/

        $this->discount_faktur = is_numeric($value) ? (int) $value : 0;

        $this->form->discount = $this->discount_faktur;

        // Refresh data faktur agar getFakturSummary memperhitungkan diskon baru
        $this->dispatch('fakturDiscountUpdated');
    }

    public function updateDiscount($cartItemId, $value)
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


        $item->update(['discount' => $value]);
        $item->update(['primary_price' => round(($item->price * (100-$value)/100) * 1.11)]);
    }

    public function delete($id)
    {
        app(CartService::class)->deleteFaktur($id);
        $this->toast('success', 'Data berhasil dihapus!');
    }

    public function getFakturSummary(): array
    {
        if ($this->faktur != null) {
            // return app(CartService::class)->getFakturSummary($this->faktur);
            return app(CartService::class)->getFakturSummary($this->faktur, $this->discount_faktur);
            
        } else {
            return [];
        }
    }

    /*public function getHeadersProperty(): array
    {
        return [
            ['key' => 'index', 'label' => 'No.', 'class' => 'w-1'],
            ['key' => 'tanggal', 'label' => 'Tanggal'],
            ['key' => 'supplier', 'label' => 'Supplier'],
            ['key' => 'product', 'label' => 'Produk'],
            ['key' => 'grand_total', 'label' => 'Total Pembelian'],
            ['key' => 'action', 'label' => 'Aksi'],
        ];
    }*/

    public function cartHeaders(): array
    {
        return [
            ['key' => 'index', 'label' => 'No.', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Produk', 'class' => 'w-1'],
            ['key' => 'expired', 'label' => 'Expired', 'class' => 'w-1'],
            ['key' => 'quantity', 'label' => 'Kuantitas', 'class' => 'w-1'],
            ['key' => 'unit', 'label' => 'Satuan', 'class' => 'w-1'],
            ['key' => 'sell', 'label' => 'Harga Beli', 'class' => 'w-1'],
            ['key' => 'discount', 'label' => 'Diskon', 'class' => 'w-1'],
            ['key' => 'tax', 'label' => 'Pajak', 'class' => 'w-1'],
            ['key' => 'primary_price', 'label' => 'Harga Pokok', 'class' => 'w-1'],
            ['key' => 'sub_total', 'label' => 'Sub Total', 'class' => 'w-1'],
            ['key' => 'action', 'label' =>  $this->readonly ? '' : 'Aksi', 'class' => 'w-1'],
        ];
    }

    public function getInvoicesProperty()
    {
        // return PurchaseInvoice::with('supplier', 'faktur.items.product')->latest()->get();
        return $this->fakturQuery()->get();
    }

    public function exportExcel()
    {
        // $fileName = 'penjualan_' . now()->format('Ymd_His') . '.xlsx';
        $fileName = 'Daftar Pembelian - ' . now()->translatedFormat('j F Y') . ' pukul ' . now()->format('H.i') . '.xlsx';

        return Excel::download(
            new InvoiceExport($this->invoices, $this->startDate, $this->endDate, $this->title),
            $fileName
        );
    }

    public function showDrawer(PurchaseInvoice $invoice = null, CartService $cartService)
    {
        // $this->loadCart($cartService);
        $this->judul = "Detail";
        $this->readonly = true;
        $this->discount_faktur = $invoice->discount;
        $this->faktur = Faktur::with('items.product')->findOrFail($invoice->faktur_id);
        // dd($invoice->faktur_id);
        
        $this->form->fillForm($invoice, $this->getFakturSummary());
        $this->showingForm = true;
    }

    public function ulang(PurchaseInvoice $invoice = null, CartService $cartService)
    {
        $this->judul = "Edit";
        $this->readonly = false;
        $this->discount_faktur = $invoice->discount;
        $this->faktur = Faktur::with('items.product')->findOrFail($invoice->faktur_id);
        if ($invoice->id !== null) {
            $this->editing = true;
        }
        
        $this->form->fillForm($invoice, $this->getFakturSummary());
        $this->resetValidation();
        $this->showingForm = true;
    }

    public function save()
    {
        // dd($this->faktur->id);
        $this->form->save($this->getFakturSummary(), $this->faktur->id);
        $message = $this->form->record->id ? 'Data berhasil diupdate' : 'Data berhasil dibuat';
        $this->success($message, 'success!', timeout: 5000);
        $this->reset();
    }

    /*public function clear(): void
    {
        $this->reset('form');
        $this->showingForm = false;
    }*/

    public function render()
    {
        // dd($this->faktur);
        if ($this->faktur === null) {
            $this->faktur = [];
        } 

        return view('livewire.purchase-invoices', [
            'cartHeaders'  => $this->cartHeaders(),
            'summaryFaktur' => $this->invoicesSummary,
            'date' => $this->dateRangeLabel,
            'headers'      => $this->headers(),
            'inventories'  => Inventory::select('id', 'inventory_name')->get()->toArray(),
            'invoices'     => $this->fakturQuery()->latest()->get(),
            // 'invoices'     => PurchaseInvoice::with('faktur.items.product')->latest()->get(),
            'judul' => $this->judul,
            'jenis_faktur' => $this->jenis_faktur,
            'payments'     => $this->payments,
            'products'     => Product::select('id', 'product_name', 'stock')->get()->toArray(),
            'summary'      => $this->getFakturSummary(),
            'suppliers'    => Supplier::select('id', 'nama')->get()->toArray(),
        ]);
    }
}
