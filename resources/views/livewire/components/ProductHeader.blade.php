<!-- ProductHeader -->
<x-header title="Data Produk" separator progress-indicato>
    <x-slot:middle class="!justify-end">
        <x-input placeholder="Search..." wire:model.live.debounce.350ms="search" clearable icon="o-magnifying-glass" />
    </x-slot:middle>

    <x-slot:actions>
        <x-button label="Tambah data" wire:click="showDrawer()" responsive icon="o-funnel" />
    </x-slot:actions>
</x-header>
