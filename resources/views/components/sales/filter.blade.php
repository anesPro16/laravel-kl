<x-drawer wire:model="filter" title="Filters" right separator with-close-button class="lg:w-1/3">
	<x-datetime label="Awal" wire:model.live.debounce.350ms="startDate" @keydown.enter="$wire.filter = false" />
	<x-datetime label="Akhir" wire:model.live.debounce.350ms="endDate" @keydown.enter="$wire.filter = false" />
	<x-slot:actions>
		<x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
		<x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.filter = false" />
	</x-slot:actions>
</x-drawer>