<?php

namespace App\Livewire;

use App\Livewire\Forms\ComponentForm;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shelf;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#Lazy
#[Title('Data Produk')]
class ProductTable extends Component
{
	use Toast, WithPagination;

	public ComponentForm $form;

  public bool $showModal = false;

  public bool $drawerForm = false;

  public $name = 'Umum';

  public string $categoryId = '';

  public string $selectedType = '';

	#[Validate('required|unique:products,category_name')]
  public string $categoryName = '';

  public string $search = '';

  public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

  public array $types = [
  	['name' => 'Alkes'],
  	['name' => 'Jasa'],
  	['name' => 'Obat'],
  	['name' => 'Umum'],
  ];

  public array $statuses = [
    ['id' => 'Dijual'],
    ['id' => 'Tidak Dijual'],
  ];

  // Table headers
  public function headers(): array
  {
    return [
      ['key' => 'index', 'label' => '#', 'class' => 'w-1'],
      ['key' => 'product_name', 'label' => 'Nama', 'class' => 'w-1'],
      ['key' => 'type', 'label' => 'Tipe', 'class' => 'w-1'],
      ['key' => 'product_code', 'label' => 'Kode Produk', 'class' => 'w-1'],
      ['key' => 'barcode', 'label' => 'Barcode', 'class' => 'w-1'],
      ['key' => 'factory_name', 'label' => 'Supplier', 'class' => 'w-1'],
      ['key' => 'unit', 'label' => 'Satuan', 'class' => 'w-1'],
      ['key' => 'purchase_price', 'label' => 'Harga Beli', 'class' => 'w-1'],
      ['key' => 'selling_price', 'label' => 'Harga Jual', 'class' => 'w-1'],
      ['key' => 'category', 'label' => 'Kategori', 'class' => 'w-1'],
      ['key' => 'shelf', 'label' => 'Rak', 'class' => 'w-1'],
      ['key' => 'stock', 'label' => 'Stok', 'class' => 'w-1'],
      ['key' => 'min_stock', 'label' => 'Min Stok', 'class' => 'w-1'],
      ['key' => 'status', 'label' => 'Status', 'class' => 'w-1'],
      ['key' => 'action', 'label' => 'Aksi', 'class' => 'w-1'],
    ];
  }

  // Clear filters
  public function clear(): void
  {
      $this->reset();
      $this->success('Filters cleared.', position: 'toast-bottom');
  }

  // Delete action
  public function delete($id): void
  {
  		Product::find($id)?->delete();
      $this->warning("Will delete #$id", 'It is fake.', position: 'toast-bottom');
  }

  public function save()
  {
    $this->form->save(); // Memanggil method save() di UserForm
    $this->toast('User berhasil ditambahkan!', 'success');
    $this->reset();
  }

  public function add(Product $product = null)
  {
    $this->form->fillForm(record: $product);
    $this->resetValidation();
    $this->drawerForm = true;
  }


  /*public function edit(Product $product)
  {
    $this->form->fillForm(record: $product);
    $this->resetValidation();
    $this->drawerForm = true;
  }*/

  // Reset pagination when any component property changes
  public function updated($property): void
  {
    if (! is_array($property) && $property != "") {
        $this->resetPage();
    }
    if ($property === 'form.type' || $property === 'form.product_name') {
        $this->form->updateProductCode();
    }
  }

  public function products(): LengthAwarePaginator
  {
    return Product::query()
    ->when($this->search, fn(Builder $q) => $q->where('product_name', 'like', "%$this->search%"))
    ->orderBy(...array_values($this->sortBy))
    ->latest('id')
    ->paginate(11);
  }

  public function render()
  {
    return view('livewire.product-table', [
    	'products' => $this->products(),
      'headers' => $this->headers(),
      'types' => $this->types,
      'statuses' => $this->statuses,
      'categories' => Category::all()->toArray(),
      'shelves' => Shelf::all()->toArray(),
      'units' => Unit::all()->toArray(),
    ]);
  }
}
