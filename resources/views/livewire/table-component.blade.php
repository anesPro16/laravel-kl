<div>
	<x-button label="Add" @click="showModal = true; $wire.openModal()" responsice icon="o-plus" class="btn-primary mb-3" />
	<x-card>
		<x-table :headers="$headers" :rows="$records" :sort-by="$sortBy" with-pagination>
			@foreach ($records as $record)
			@scope('cell_index', $record)
			{{ $loop->iteration }}
			@endscope
			@scope('cell_actions', $record)
			<div class="flex justify-center space-x-2">
				<x-button icon="o-pencil-square" @click="showModal = true; $wire.openModal('{{ $record->id }}')" class="btn-ghost btn-sm text-blue-500" />
				<x-button icon="o-trash" wire:click="delete('{{ $record->id }}')" wire:confirm="Are you sure?" class="btn-ghost btn-sm text-red-500" />
				</div>
				@endscope
				@endforeach
			</x-table>
		</x-card>

		<!-- Modal Form -->
		@if ($showModal)
		<x-modal wire:model="showModal" x-show="showModal" title="Form {{ Str::headline(class_basename(get_class($this->model))) }}" subtitle="Livewire example">
		    <x-form wire:submit.prevent="save">
		        <x-input label="Name" wire:model.defer="fieldName" />

		        <x-slot:actions>
		            <x-button label="Cancel" @click="showModal = false" />
		            <x-button label="Save" class="btn-primary" type="submit" />
		        </x-slot:actions>
		    </x-form>    
		</x-modal>

	@endif

</div>