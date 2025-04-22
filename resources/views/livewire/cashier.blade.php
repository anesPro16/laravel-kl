<div>
  <div class="mt-6 space-y-3 border-t pt-4">
  <div class="flex justify-between">
    <span class="font-semibold">Subtotal</span>
    <span class="text-right">Rp{{ number_format($summary['subtotal']) }}</span>
  </div>

  <div class="flex justify-between items-center space-x-4">
    <div class="flex-1">
      <label class="text-sm text-gray-500">Diskon</label>
      <input 
        type="number" 
        wire:model.blur="discount" 
        class="w-10 border rounded p-1  @error('discount') border-red-500 @enderror" 
        placeholder="Diskon"
        min="0"
        max="{{ $discount_type === 'percent' ? 50 : null }}">
        @error('discount')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div class="w-10">
      <select wire:model.live="discount_type" class="w-full border rounded p-1">
        <option value="percent">%</option>
        <option value="nominal">Rp</option>
      </select>
    </div>
    {{-- <span>{{$summary['discount']}}</span> --}}
  </div>

  <div class="flex justify-between">
    <span class="font-semibold">Total</span>
    <span class="text-right">Rp{{ number_format($summary['grand_total']) }}</span>
  </div>

  <div class="flex justify-between items-center">
    <label class="font-semibold">Bayar</label>
    <input type="number" wire:model.blur="paid_amount" class="w-32 border rounded p-1 text-right" placeholder="Bayar">
  </div>

  @error('paid_amount')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror

  <div class="flex justify-between">
    <span class="font-semibold">Kembalian</span>
    <span class="text-right">{{$summary['change']}}</span>
  </div>

  
<div class="my-4 flex justify-between items-center">
  <button
    wire:click="checkout"
    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
  >
    Bayar
  </button>
</div>

  @if (session()->has('success'))
  <div class="my-2 text-green-600">
    {{ session('success') }}
  </div>
@endif
</div>

{{-- LOGIN DULU!!! LOGIN DULU!!! LOGIN DULU!!! LOGIN DULU!!! LOGIN DULU!!! --}}
<div class="my-4"></div>
	<x-choices-offline
    placeholder="Cari Produk"
     wire:model.live="selectedProductId"
    :options="$products"
    optionValue="id"
    optionLabel="product_name"
    searchable 
  />

  <x-table :headers="$headers" :rows="$cart->items">
    @scope('cell_index', $product)
      {{ $loop->iteration }}
    @endscope
    @foreach ($cart->items as $item)
      @scope('cell_name', $item)
        {{ $item->product->product_name }}
      @endscope
      @scope('cell_quantity', $item)
        <div x-data="{ qty: {{$item->quantity}} }">
         <button x-show="qty > 1" x-on:click="$wire.updateQuantity({{ $item->id }}, qty - 1); qty--" icon="o-minus">-</button>
          <input
            type="number"
            min="1"
            max="{{ $item->product->stock }}"
            x-model.number="qty"
            x-on:change="$wire.updateQuantity({{ $item->id }}, qty)"
            class="w-12 text-center border border-gray-300 rounded"
        />
         <button x-show="qty < {{ $item->product->stock }}" x-on:click="$wire.updateQuantity({{ $item->id }}, qty + 1); qty++" icon="o-plus">+</button>
        </div>
      @endscope

      @scope('cell_unit', $item)
        {{ $item->product->unit }}
      @endscope

      @scope('cell_price', $item)
        <x-select :options="[
          ['id' => 1, 'name' => 'Harga Utama'],
          ['id' => 2, 'name' => 'Harga Pokok tertinggi'],
          ['id' => 3, 'name' => 'Harga Custom'],
          ['id' => 4, 'name' => 'Harga Diskon'],
        ]"/>
      @endscope

      @scope('cell_sell', $item)
        <div x-data="{ sell: {{$item->product->selling_price}} }">
         <span x-text="sell"></span>
        </div>
      @endscope

      @scope('cell_sub_total', $item)
        <div x-data="{ sum: {{$item->quantity * $item->product->selling_price}} }">
         <span x-text="sum"></span>
        </div>
      @endscope

      @scope('cell_action', $item)
        <x-button icon="o-trash" wire:click="delete({{ $item->id }})" wire:confirm="Are you sure?" spinne class="btn-ghost btn-sm text-red-500" />
      @endscope

    @endforeach
  </x-table>
    	
    

</div>