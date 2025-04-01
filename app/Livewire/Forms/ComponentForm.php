<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ComponentForm extends Form
{
    public ?Product $record;

    public ?string $productId = null;

    #[Validate('required|unique:products,product_name, onUpdate: false')]
    public string $product_name = '';

    #[Validate('required')]
    public string $type = '';

    #[Validate('required')]
    public string $product_code = '';

    #[Validate('required')]
    public string $barcode = '';

    #[Validate('required')]
    public string $factory_name = '';

    #[Validate('required')]
    public string $unit = '';

    #[Validate('required|numeric|min:0')]
    public ?string $purchase_price = '';

    #[Validate('required|numeric|min:0')]
    public ?string $selling_price = '';

    #[Validate('required')]
    public string $category = '';

    #[Validate('required')]
    public string $shelf = '';

    #[Validate('required|integer|min:0')]
    public ?string $stock = '';

    #[Validate('nullable|integer|min:0')]
    public ?string $min_stock = '';

    #[Validate('required|in:Dijual,Tidak Dijual')]
    public string $status = '';

    public function fillForm(Product $record = null)
    {
        $this->record = $record;

        $this->product_name = $record->product_name ?? '';
        $this->type = $record->type ?? 'Obat';
        $this->updateProductCode();
        $this->barcode = $record->barcode ?? '';
        $this->factory_name = $record->factory_name ?? '';
        $this->unit = $record->unit ?? '';
        $this->purchase_price = $record->purchase_price ?? null;
        $this->selling_price = $record->selling_price ?? null;
        $this->category = $record->category ?? '';
        $this->shelf = $record->shelf ?? '';
        $this->stock = $record->stock ?? '';
        $this->min_stock = $record->min_stock ?? '';
        $this->status = $record->status ?? 'Dijual';
    }

    public function updatedType()
    {
        $this->updateProductCode();
    }

    public function updateProductCode()
    {
        // $this->product_code = strtoupper(substr($this->type, 0, 2)) . '-' . strtoupper(substr($this->product_name, 0, 3)) . '-00001';
        $kode = strtoupper(substr($this->type, 0, 2)) . '-' . strtoupper(substr($this->product_name, 0, 3));
        // Cek apakah kode sudah ada di database
        $lastProduk = Product::where('product_code', 'like', $kode . '%')->orderBy('product_code', 'desc')->first();

        if ($lastProduk) {
            // Ambil angka terakhir dan tambahkan 1
            preg_match('/\d+$/', $lastProduk->product_code, $matches);
            $increment = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
        } else {
            $increment = 1;
        }

        $this->product_code = $kode . '-' . $increment;
    }

    public function save()
    {
        $this->validate([
            'product_name' => 'required|unique:products,product_name,' . $this->record->id,
            'type' => 'required',
            'product_code' => 'required|unique:products,product_code,' . $this->record->id,
            'barcode' => 'required',
            'factory_name' => 'required',
            'unit' => 'required',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'category' => 'required',
            'shelf' => 'required',
            'stock' => 'required',
            'min_stock' => 'required',
            'status' => 'required',
        ]);

        Product::updateOrCreate(
            ['id' => $this->record->id ?: Str::ulid()],
            $this->only([
                'product_name',
                'type',
                'product_code',
                'barcode',
                'factory_name',
                'unit',
                'purchase_price',
                'selling_price',
                'category',
                'shelf',
                'stock',
                'min_stock',
                'status',
            ])
        );

    }
}