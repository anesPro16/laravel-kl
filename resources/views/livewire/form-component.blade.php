<x-modal wire:model="showModal" x-show="showModal" title="Form {{ ucfirst($fieldName) }}" subtitle="Livewire example">
    <x-form wire:submit.prevent="save">
        <x-input label="{{ ucfirst($fieldName) }}" wire:model="{{ $fieldName }}" />                  
        
        <x-slot:actions>
            <x-button label="Cancel" x-on:click="showModal = ! showModal" />
            <x-button label="Save" class="btn-primary" type="submit" />
        </x-slot:actions>
    </x-form>    
</x-modal>
