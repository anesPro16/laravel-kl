{{-- <x-modal wire:model="showModal" x-data="{ open: @entangle('showModal') }" x-show="open" title="Form Kategori"> --}}
<x-modal wire:model="showModal"  x-show="open" title="Form Kategori">
    <x-form wire:submit.prevent="save">
        <x-input label="Nama Kategori" wire:model="categoryName" />

        <x-slot:actions>
            <x-button label="Cancel" x-on:click="open = false" />
            <x-button label="Save" class="btn-primary" type="submit" />
        </x-slot:actions>
    </x-form>
</x-modal>
