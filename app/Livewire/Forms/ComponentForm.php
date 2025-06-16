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

    public string $product_name = '';

    public string $type = '';

    public string $product_code = '';

    public string $barcode = '';

    // public string $factory_name = '';
    public ?string $supplier_id = null;

    public string $unit = '';

    public ?string $purchase_price = '';

    public ?string $selling_price = '';

    public string $category = '';

    public string $shelf = '';

    public ?string $stock = '';

    public ?string $min_stock = '';

    public string $status = '';

    public function fillForm(Product $record = null)
    {
        $this->record = $record;

        $this->product_name = $record->product_name ?? '';
        $this->type = $record->type ?? 'Obat';
        if ($record == null) {
            updateProductCode();
        } else {
            $this->product_code = $record->product_code ?? '';
        }
        $this->barcode = $record->barcode ?? '';
        $this->supplier_id = $record->supplier_id ?? '';
        $this->unit = $record->unit ?? '';
        $this->purchase_price = $record->purchase_price ?? null;
        $this->selling_price = $record->selling_price ?? null;
        $this->category = $record->category ?? '';
        $this->shelf = $record->shelf ?? '';
        $this->stock = $record->stok ?? '';
        $this->min_stock = $record->min_stock ?? '';
        $this->status = $record->status ?? 'Dijual';
    }

    public function updatedType()
    {
        $this->updateProductCode();
    }

    public function updateProductCode()
    {
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
            'supplier_id' => 'required',
            'product_name' => 'required|unique:products,product_name,' . $this->record->id,
            'type' => 'required',
            'product_code' => 'required|unique:products,product_code,' . $this->record->id,
            'barcode' => 'required',
            'unit' => 'required',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'category' => 'required',
            'shelf' => 'required',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'status' => 'required|in:Dijual,Tidak Dijual',
        ]);

        Product::updateOrCreate(
            ['id' => $this->record->id ?: Str::ulid()],
            $this->only([
                'supplier_id',
                'product_name',
                'type',
                'product_code',
                'barcode',
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