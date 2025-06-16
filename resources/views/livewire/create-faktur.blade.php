<div>
	<x-theme-toggle class="btn btn-circle btn-ghost" />
	<x-form wire:submit.prevent="save">
				<div class="flex flex-row justify-between items-end flex-wrap">

					<div class="basis-1/4">
						<x-choices-offline
						label="Supplier"
						wire:model="form.supplier_id"
						:options="$suppliers"
						optionLabel="nama"
						placeholder="Search ..."
						single
						searchable />
					</div>

					<div class="">
						<x-button icon="o-plus"  class="btn-primary text-white" />
					</div>

					<div class="basis-1/6">
						<x-input label="No. Surat Pesan" wire:model.defer="form.no_surat_pesan" />
					</div>

					<div class="basis-1/6">
						<x-input label="No. Faktur" wire:model.defer="form.no_faktur" />
					</div>

					<div class="basis-1/6">
						<x-datetime label="Tanggal Faktur" wire:model.defer="form.tanggal" />
					</div>

					<div class="basis-1/6">
						<x-datetime label="Tanggal Penerimaan" wire:model.defer="form.tgl_penerimaan" />
					</div>

					<div class="basis-1/4">
						<x-select label="Jenis Faktur" wire:model.defer="form.jenis_faktur" :options="$jenis_faktur" optionLabel="id" />
					</div>

					<div class="basis-1/6">
						<x-choices-offline
						label="Gudang Penerima"
						wire:model="form.inventory_id"
						:options="$inventories"
						optionLabel="inventory_name"
						placeholder="Search ..."
						single
						searchable />
					</div>

					<div class="basis-1/6">
						<x-select label="Jenis Pembayaran" wire:model.defer="form.jenis_pembayaran" :options="$payments" optionLabel="id" />
					</div>

					<div class="basis-1/6">
						<x-input label="Tempo Bayar (hari)" type="number" wire:model.defer="form.tempo_bayar" />
					</div>

					<div class="basis-1/6">
						<x-datetime label="Jatuh Tempo" wire:model.live.debounce.350ms="form.jatuh_tempo" />
					</div>
					<x-slot:actions>
						<x-button class="btn-primary" type="submit" label="Simpan" />
						<x-button label="Reset" icon="o-x-mark" wire:click="clear" />
				</x-slot:actions>
			</div>
		{{-- </x-form> --}}
		

	<x-choices-offline
    placeholder="Cari Produk"
     wire:model.live="selectedProductId"
    :options="$products"
    optionValue="id"
    optionLabel="product_name"
    optionSubLabel="stock"
    searchable 
  />

		<x-table :headers="$cartHeaders" :rows="$faktur->items">
    @scope('cell_index', $product)
      {{ $loop->iteration }}
    @endscope
    @foreach ($faktur->items as $item)
      @scope('cell_name', $item)
        {{ $item->product->product_name }}
      @endscope

      @scope('cell_expired', $item)
        {{-- {{ $item->product->product_name }} --}}
        <x-datetime wire:model.live.debounce.350ms="expired" />
      @endscope
      @scope('cell_quantity', $item)
        <div x-data="{ qty: {{$item->quantity}} }">
         {{-- <button x-show="qty > 1" x-on:click="$wire.updateQuantity({{ $item->id }}, qty - 1); qty--" icon="o-minus">-</button> --}}
          <input
            type="number"
            min="1"
            max="{{ $item->product->stock }}"
            x-model.number="qty"
            x-on:blur="$wire.$refresh()"
            x-on:change="$wire.updateQuantity({{ $item->id }}, qty);"
            class="w-12 text-center border border-gray-300 rounded"
        />
         {{-- <button x-show="qty < {{ $item->product->stock }}" x-on:click="$wire.updateQuantity({{ $item->id }}, qty + 1); qty++" icon="o-plus">+</button> --}}
        </div>
      @endscope

      @scope('cell_unit', $item)
        {{ $item->product->unit }}
      @endscope

      @scope('cell_sell', $item)
        <div x-data="{ price: {{$item->price}} }">
          <input
            type="number"
            x-model.number="price"
            x-on:blur="$wire.$refresh()"
            x-on:change="$wire.updatePriceAtTime({{ $item->id }}, price);"
            class="w-32 border rounded p-1 text-right"
            min="0"
          >
        </div>
      @endscope

      @scope('cell_tax', $item)
      	{{$item->tax}}%
      @endscope

      @scope('cell_discount', $item)
      	<div x-data="{ discount: {{$item->discount}} }">
	      	<input 
	        type="number" 
	        {{-- wire:model.blur="discount" --}}
	        x-model.number="discount"
          x-on:blur="$wire.$refresh()"
	        x-on:change="$wire.updateDiscount({{ $item->id }}, discount);"
	        class="w-20 border rounded p-1" 
	        placeholder="Diskon"
	        min="0"
	        max="50">%
	       </div>
      	{{-- <x-radio :options="[['id' => '%'],['id' => 'Rp']]" optionValue="id" optionLabel="id" wire:model.live="discount_type" /> --}}
      @endscope

      @scope('cell_sub_total', $item)
      	{{$item->quantity * $item->primary_price}}
      	{{-- {{$item->quantity * round($item->price * (100 + $item->tax)/100)}} --}}
      @endscope

      @scope('cell_action', $item)
        <x-button icon="o-trash" wire:click="delete({{ $item->id }})" wire:confirm="Are you sure?" spinne class="btn-ghost btn-sm text-red-500" />
      @endscope

    @endforeach
  </x-table>

		  <div class="w-32">
				{{-- <x-input label="Grand Total" wire:model.live="grand_total" /> --}}
			</div>

			<div x-data="{ grand_total: {{$summary['subtotal']}} }">

				<label id="total">Sub Total</label>
				
		    <input
		    	id="total"
		      type="number"
		      value="{{$summary['subtotal']}}"
		      class="w-32 border rounded p-1 text-right"
		      min="0"
		      readonly
		    >
		  </div>
			
			
		  <div x-data="{ discount_faktur: {{$discount_faktur}} }">
		  	<label id="diskon">Diskon : </label>
      	<x-input 
      		class="w-1" 
      		type="number"
      		min="0"
   				max="100"
      		wire:model.defer="form.discount"
    			x-on:change="$wire.updateDiscountFaktur($event.target.value)" />%
      </div>

      <div x-data="{ ppn: {{$summary['ppn']}} }">
		  	<label id="ppn">PPN(11%) : </label>
      	<input 
        type="number"
        value="{{$summary['ppn']}}"
        class="w-20 border rounded p-1" 
        min="0"
        readonly>
      </div>

      <label>Total  Rp{{ number_format($summary['total']) }}</label>

		</x-form>
</div>
