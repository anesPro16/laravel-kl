<div x-data="{ showModal: false }">
    <!-- HEADER -->
    <x-header title="{{ $title }}" class="mb-3" separator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce.350ms="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TABEL -->
    <livewire:table-component 
    :headers="$this->headers()"
    :model="$model"
    :fieldName="$fieldName"
    {{-- :search="$search" --}}
    :sortBy="$sortBy"
    />

</div>
