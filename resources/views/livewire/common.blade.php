<div>
	<x-header :title="$title" separator progress-indicato>
	<x-slot:middle class="!justify-end">
		{{-- <x-input placeholder="Search..." wire:model.live.debounce.350ms="search" clearable icon="o-magnifying-glass" /> --}}
    <div class="flex mx-5">
			<x-select
         wire:model.live="selectedRange"
        :options="$rentang"
        optionValue="name"
        optionLabel="name"
      />
				<p class="my-3 mx-5 bg-blue-100">{{ $date }}</p>
      	
      </div>
		{{-- <x-button label="Filters" @click="$wire.filter = true" responsive icon="o-funnel" /> --}}
	</x-slot:middle>
</x-header>

<p>Total Penjualan: <strong>{{ $summary['count'] }}</strong></p>
<p>Retur Penjualan: <strong>{{ $returCount}}</strong></p>
<p>Penjualan Tertolak: <strong>{{ $rejectCount}}</strong></p>


<p>Database Produk: <strong>{{ $productCount}}</strong></p>
<p>Database Supplier: <strong>{{ $supplierCount}}</strong></p>
</div>
