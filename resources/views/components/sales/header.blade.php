<x-header :title="$title" separator progress-indicato>
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