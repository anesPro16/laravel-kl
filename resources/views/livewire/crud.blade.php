<div x-data="{ showModal: false }">
    <!-- HEADER -->
    <x-header title="{{ Str::headline(class_basename(get_class($this->model))) }}" separator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce.350ms="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TOMBOL TAMBAH -->
    <x-button label="Add" @click="showModal = true; $wire.openModal()" responsice icon="o-plus" class="btn-primary" />

    <!-- TABEL -->
    <livewire:table-component :records="$records" :headers="$headers" :sortBy="$sortBy" />

    <!-- FORM MODAL -->
    <livewire:form-component />
</div>
