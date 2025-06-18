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
        class="w-20 border rounded p-1  @error('discount') border-red-500 @enderror" 
        placeholder="Diskon"
        min="0"
        max="{{ $discount_type === '%' ? 50 : null }}">
        @error('discount')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div class="w-20">
      <x-radio label="Diskon" :options="$types" optionValue="id" optionLabel="id" wire:model.live="discount_type" />
    </div>
    {{-- <span>{{$summary['discount']}}</span> --}}
  </div>

  <div class="flex justify-between">
    <span class="font-semibold">Total</span>
    {{$summary['grandTotal']}}
  </div>

  <div class="flex justify-end">
    {{-- <span class="font-semibold">Uang Tunai</span> --}}
    <x-toggle label="Uang Tunai" wire:model.live="paid_type" right/>
  </div>
{{-- {{ dd($paid_type) }} --}}
  
  @if ($paid_type === false)
    <div class="flex justify-between">
      <span class="font-semibold">Metode Pembayaran</span>
      <x-select
      placeholder="Metode Pembayaran"
         wire:model.live="selectedMethod"
        :options="$paid_methods"
        optionValue="name"
        optionLabel="name"
        {{-- @readonly($paid_type != false) --}}
      />
    </div>
  @endif

  <div class="flex justify-between items-center">
    <label class="font-semibold">Bayar</label>
    <input 
      type="number" 
      wire:model.blur="paid_amount" 
      x-on:click="$wire.$refresh()"
      class="w-32 border rounded p-1 text-right" 
      min="0"
      placeholder="Bayar">
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
            {{-- max="{{ $item->product->stock }}" --}}
            max="{{ $item->product->stok }}"
            x-model.number="qty"
            x-on:blur="$wire.$refresh()"
            x-on:change="$wire.updateQuantity({{ $item->id }}, qty);"
            class="w-12 text-center border border-gray-300 rounded"
        />
         <button x-show="qty < {{ $item->product->stok }}" x-on:click="$wire.updateQuantity({{ $item->id }}, qty + 1); qty++" icon="o-plus">+</button>
        </div>
      @endscope

      @scope('cell_unit', $item)
        {{ $item->product->unit }}
      @endscope

      @foreach ($cart->items as $index => $item)
  @scope('cell_price', $item)
    <select 
      class="border rounded p-1"
      wire:change="updateOptionPrice({{ $item['id'] }}, {{ $item['product']['selling_price'] }}, $event.target.value)"
    >
      <option value="1" @selected($item['option'] == 1)>Harga Klinik</option>
      <option value="2" @selected($item['option'] == 2)>Harga Bebas</option>
      <option value="3" @selected($item['option'] == 3)>Harga Resep</option>
      <option value="4" @selected($item['option'] == 4)>Harga Custom</option>
    </select>
  @endscope
@endforeach




      @scope('cell_sell', $item)
        <div x-data="{ price_at_time: {{$item->price_at_time}} }">
          <input
            type="number"
            {{-- value="{{$item->price_at_time}}" --}}
            x-model.number="price_at_time"
            x-on:blur="$wire.$refresh()"
            x-on:change="$wire.updatePriceAtTime({{ $item->id }}, price_at_time);"
            class="w-32 border rounded p-1 text-right"
            min="0"
            @disabled($item->option != 4)
            @readonly($item->option != 4)
          >
        </div>
      @endscope

      @scope('cell_sub_total', $item)
        {{-- <div x-data="{ sum: {{$item->quantity * $item->product->selling_price}} }"> --}}
        <div x-data="{ sum: {{$item->quantity * $item->price_at_time}} }">
         <span x-text="sum"></span>
        </div>
      @endscope

      @scope('cell_action', $item)
        <x-button icon="o-trash" wire:click="delete({{ $item->id }})" wire:confirm="Are you sure?" spinne class="btn-ghost btn-sm text-red-500" />
      @endscope

    @endforeach
  </x-table>    	
    

</div>