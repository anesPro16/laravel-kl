<x-button 
label="Detail" 
wire:click="showDetail({{ $sale->id }})"
class="btn-ghost btn-sm text-blue-500" 
responsive 
/>
<x-dropdown>
	<x-slot:trigger>
		<x-button icon="o-ellipsis-vertical"/>
	</x-slot:trigger>
	<x-menu-item title="Export" link="/cashier"/>
	<x-menu-item title="Cetak Pdf" />
	<x-menu-item title="Retur" wire:click="retur({{ $sale->id }})"/>
	<x-menu-item title="Tolak" wire:click="reject({{ $sale->id }})"/>
	<x-menu-item title="Buat ulang" />
	<x-menu-item title="Batalkan" />
</x-dropdown>
