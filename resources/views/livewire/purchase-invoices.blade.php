<div>
	<x-card>
		<x-header title="Faktur Pembelian">
			<x-slot:middle class="!justify-end">
			<x-input placeholder="Search..." wire:model.live.debounce.350ms="search" clearable icon="o-magnifying-glass" />
			</x-slot:middle>
			<x-slot:actions>
				<x-button wire:click="exportExcel" class="bg-green-500 hover:bg-green-600 text-white">
			    Export Excel
				</x-button>
				<x-button label="Filters" @click="$wire.filter = true" responsive icon="o-funnel" />
			</x-slot:actions>
		</x-header>

		{{-- <x-button icon="o-plus" label="Input Faktur" wire:click="showDrawer()" class="btn-primary btn-sm" /> --}}
		{{-- <x-button icon="o-plus" label="Input Faktur" link="/create-faktur" class="btn-primary btn-sm" /> --}}
		<x-button icon="o-plus" label="Input Faktur" wire:click="createFaktur()" class="btn-primary btn-sm" />

		<x-table :headers="$headers" :rows="$invoices" containerClass>

			@scope('cell_index', $invoice)
			{{ $loop->iteration }}
			@endscope

			@scope('cell_supplier', $invoice)
			{{ $invoice->supplier->nama }}
			@endscope

			@scope('cell_tanggal', $invoice)
			{{ $invoice->tanggal->format('d-m-Y') }}
			@endscope

			@scope('cell_product', $invoice)
        @php
            $items = $invoice->faktur->items;
            $firstProduct = $items->first()?->product->product_name ?? '-';
            $qty = $items->first()->quantity ?? 0;
            $unit = $items->first()?->product->unit ?? '-';
            $productCount = $items->count();
        @endphp
        <span class="{{ ($invoice->status != 'process') ? 'text-red-500' : ''}}">
        	{{ $qty }} {{ $unit . ' x'}} {{ $firstProduct }}
        </span>
        @if ($productCount > 1)
            <x-badge value="+ {{ $productCount - 1 }} lainnya" class="badge-info text-sm mx-3" />
        @endif
  		  @endscope

				@scope('cell_action', $invoice)
					<div class="flex space-x-1">
						<x-button label="Detail" wire:click="showDrawer({{ $invoice->id }})" />
						<x-dropdown>
              <x-slot:trigger>
                  <x-button icon="o-ellipsis-vertical"/>
              </x-slot:trigger>
              {{-- <x-menu-item title="Export Pdf" link="{{ route('sales.export.pdf', $invoice->id) }}" external /> --}}
              <x-menu-item title="Export Pdf" onclick="exportPDF({{ $invoice->id }})" external />
              <x-menu-item title="Buat Ulang" wire:click="ulang({{ $invoice->id }})" />
              <x-menu-item title="Retur" wire:click="retur({{ $invoice->id }})"/>
              <x-menu-item title="Batalkan" wire:click="reject({{ $invoice->id }})"/>
          </x-dropdown>
					</div>
				@endscope
			</x-table>
		</x-card>

		@if($showingForm)
		<x-drawer title="{{ $judul}} Faktur Pembelian" wire:model="showingForm" with-close-button class="w-full">
			{{-- <div x-data="{ readonly: @js($readonly) }"> --}}
			{{-- <x-form wire:submit.prevent="save"> --}}
			{{-- <x-form wire:submit.prevent="save" x-data="{ readonly: @js($readonly) }"> --}}
				<x-form wire:submit.prevent="save" x-data="{ readonly: $wire.entangle('readonly') }">

				<div class="flex flex-row justify-between items-end flex-wrap">

					<div class="basis-1/4">
						<x-choices-offline
						label="Supplier"
						wire:model.defer="form.supplier_id"
						x-bind:readonly="readonly"
						x-bind:disabled="readonly"
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
						<x-input label="No. Surat Pesan" wire:model.defer="form.no_surat_pesan" x-bind:readonly="readonly"/>
					</div>

					<div class="basis-1/6">
						<x-input label="No. Faktur" wire:model.defer="form.no_faktur" x-bind:readonly="readonly"/>
					</div>

					<div class="basis-1/6">
						<x-datetime label="Tanggal Faktur" wire:model.defer="form.tanggal" x-bind:readonly="readonly"/>
					</div>

					<div class="basis-1/6">
						<x-datetime label="Tanggal Penerimaan" wire:model.defer="form.tgl_penerimaan" x-bind:readonly="readonly"/>
					</div>

					<div class="basis-1/4">
						<x-select label="Jenis Faktur" wire:model.defer="form.jenis_faktur" :options="$jenis_faktur" optionLabel="id" x-bind:readonly="readonly" x-bind:disabled="readonly"/>
					</div>

					<div class="basis-1/6">
						<x-choices-offline
						label="Gudang Penerima"
						wire:model="form.inventory_id"
						x-bind:readonly="readonly"
						x-bind:disabled="readonly"
						:options="$inventories"
						optionLabel="inventory_name"
						placeholder="Search ..."
						single
						searchable />
					</div>

					<div class="basis-1/6">
						<x-select label="Jenis Pembayaran" wire:model.defer="form.jenis_pembayaran" :options="$payments" optionLabel="id" x-bind:readonly="readonly" x-bind:disabled="readonly"/>
					</div>

					<div class="basis-1/6">
						<x-input label="Tempo Bayar (hari)" type="number" wire:model.defer="form.tempo_bayar" x-bind:readonly="readonly"/>
					</div>

					<div class="basis-1/6">
						<x-datetime label="Jatuh Tempo" wire:model.live.debounce.350ms="form.jatuh_tempo" x-bind:readonly="readonly"/>
					</div>
					<x-slot:actions>
						<template x-if="!readonly">
							<x-button class="btn-primary" type="submit" label="Simpan" />
						</template>
				</x-slot:actions>
			</div>
		{{-- </x-form> --}}
		
	<x-choices-offline
    placeholder="Cari Produk"
     wire:model.live="selectedProductId"
     x-bind:readonly="readonly"
     x-bind:disabled="readonly"
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
      @scope('cell_quantity', $item)
        <div x-data="{ qty: {{$item->quantity}} }">
          <input
            type="number"
            min="1"
            max="{{ $item->product->stock }}"
            x-model.number="qty"
            x-bind:readonly="readonly"
            x-on:blur="$wire.$refresh()"
            x-on:change="$wire.updateQuantity({{ $item->id }}, qty);"
            class="w-12 text-center border border-gray-300 rounded"
        />
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
            x-bind:readonly="readonly"
            {{-- x-on:blur="$wire.$refresh()" --}}
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
	        wire:model.blur="discount"
	        x-bind:readonly="readonly"
	        x-model.number="discount"
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
      <template x-if="!readonly">
        <x-button icon="o-trash" wire:click="delete({{ $item->id }})" wire:confirm="Are you sure?" spinne class="btn-ghost btn-sm text-red-500" />
        </template>
      @endscope

    @endforeach
  </x-table>

		  <div class="w-32">
				{{-- <x-input label="Grand Total" wire:model.live="grand_total" /> --}}
			</div>

			<label id="subtotal">Sub Total : Rp{{ number_format($summary['subtotal']) }} </label>
			<div x-data="{ grand_total: {{$summary['subtotal']}} }">
		    <input
		    	id="subtotal"
		      type="number"
		      hidden
		      value="{{$summary['subtotal']}}"
		      class="w-32 border rounded p-1 text-right"
		      min="0"
		      readonly
		    >
		  </div>

		  <div x-data="{ discount_faktur: {{$discount_faktur}} }">
		  	<label id="diskon">Diskon : </label>
      	<x-input 
      		class="w-1" type="number"
      		x-model.number="discount_faktur"
      		x-bind:readonly="readonly"
          x-on:blur="$wire.$refresh()"
	        x-on:change="$wire.updateDiscountFaktur(discount_faktur);" 
      		wire:model.defer="form.discount" />%
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
	{{-- </div> --}}
	</x-drawer>
	@endif

	<x-sales.summary :summary="$summaryFaktur" :date="$date" :$title />
	<x-sales.filter />
</div>

<script>
    function exportPDF(id) {
        const isMobile = window.innerWidth <= 768;
        const mode = isMobile ? 'mobile' : 'desktop';
        const url = `/invoice/${id}/faktur-pdf?mode=${mode}`;
        window.open(url, '_blank');
    }
</script>