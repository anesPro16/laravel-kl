<div x-data="{ showModal: false }">
    <x-header title="Units" separator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce.350ms="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <x-button label="Add" @click="showModal = true; $wire.openModal()" responsice icon="o-plus" class="btn-primary" />

    <livewire:table-component />
    <livewire:form-component />
</div>

