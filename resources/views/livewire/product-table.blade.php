<div>
	<!-- HEADER -->
	<x-header title="Hello" separator progress-indicato>
		<x-slot:middle class="!justify-end">
		<x-input placeholder="Search..." wire:model.live.debounce.350ms="search" clearable icon="o-magnifying-glass" />
	</x-slot:middle>
	<x-slot:actions>
		{{-- <x-button label="Tambah data" @click="$wire.drawerForm = true" responsive icon="o-funnel" /> --}}
		<x-button label="Tambah data" wire:click="add()" responsive icon="o-funnel" />
	</x-slot:actions>
</x-header>

<!-- TABLE  -->
{{-- {{ dd($products) }} --}}
<x-card>
	<x-table :headers="$headers" :rows="$products" :sort-by="$sortBy" with-pagination>
		@scope('cell_index', $product)
		{{ $loop->iteration }}
		@endscope
		@scope('cell_action', $product)
		<div class="flex space-x-2">
			<x-button icon="o-pencil" wire:click="add('{{ $product->id }}')" class="btn-ghost btn-sm text-blue-500" />
			<x-button icon="o-trash" wire:click="delete('{{ $product->id }}')" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
		</div>
		@endscope
	</x-table>
</x-card>

<!-- DRAWER -->
<x-drawer wire:model="drawerForm" title="Form Produk" subtitle="Informasi Dasar Produk" right separator with-close-button class="xl:w-6/12 lg:w-8/12 md:w-11/12 ">

	<x-form wire:submit.prevent="save">
			<div class="flex justify-between flex-wrap">
				<div class="w-80 lg:w-2/5">
					<x-radio label="Tipe Produk" :options="$types" optionValue="name" wire:model.live.debounce.250ms="form.type" />
					<x-input label="Nama Produk" wire:model.blur="form.product_name" />
			    <x-input label="Kode Produk" wire:model="form.product_code" />
			    <x-input label="Barcode" wire:model="form.barcode" />
					<x-input label="Nama Pabrik" wire:model="form.factory_name" />
					<x-choices-offline
				    label="Satuan"
				    wire:model="form.unit"
				    :options="$units"
				    optionValue="name"
				    placeholder="Search ..."
				    single
				    searchable />
					<x-input label="Harga Beli" wire:model="form.purchase_price" />
				{{-- {{ dd($categories) }} --}}
				</div>
				<div class="w-80 lg:w-2/5">
					<x-input label="Harga Jual" wire:model="form.selling_price" />
				  <x-choices-offline
				    label="Kategori"
				    wire:model="form.category"
				    :options="$categories"
				    optionValue="name"
				    placeholder="Search ..."
				    single
				    searchable />
					<x-choices-offline
				    label="Rak"
				    wire:model="form.shelf"
				    :options="$shelves"
				    optionValue="name"
				    placeholder="Search ..."
				    single
				    searchable />
					<x-input label="Stok" wire:model="form.stock" />
					<x-input label="Minimal" wire:model="form.min_stock" />
					<x-radio label="Status Jual" :options="$statuses" option-label="id" wire:model="form.status" />
				    
				</div>
			</div>
		<x-slot:actions>
			<x-button label="Cancel" type="reset" x-on:click="open = false" />
			<x-button label="Save" class="btn-primary" type="submit" />
		</x-slot:actions>
	</x-form>

</x-drawer>
</div>
