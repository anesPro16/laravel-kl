<!-- ProductDrawer -->
<x-drawer wire:model="drawerForm" title="Form Produk" subtitle="Informasi Dasar Produk" right separator with-close-button class="xl:w-6/12 lg:w-8/12 md:w-11/12">
    <x-form wire:submit.prevent="save">
        <div class="flex justify-between flex-wrap">
            <div class="w-80 lg:w-2/5">
                <x-radio label="Tipe Produk" :options="$types" optionValue="name" wire:model.live.debounce.250ms="form.type" />
                <x-input label="Nama Produk" wire:model.blur="form.product_name" />
                <x-input label="Kode Produk" wire:model="form.product_code" />
                <x-input label="Barcode" wire:model="form.barcode" />
                <x-input label="Nama Pabrik" wire:model="form.factory_name" />
                <x-choices-offline
                    label="Satuan"
                    wire:model="form.unit"
                    :options="$units"
                    optionValue="unit_name"
                    optionLabel="unit_name"
                    placeholder="Search ..."
                    single
                    searchable />
                <x-input label="Harga Beli" wire:model="form.purchase_price" />
            </div>

            <div class="w-80 lg:w-2/5">
                <x-input label="Harga Jual" wire:model="form.selling_price" />
                <x-choices-offline
                    label="Kategori"
                    wire:model="form.category"
                    :options="$categories"
                    optionValue="category_name"
                    optionLabel="category_name"
                    placeholder="Search ..."
                    single
                    searchable />
                <x-choices-offline
                    label="Rak"
                    wire:model="form.shelf"
                    :options="$shelves"
                    optionValue="shelf_name"
                    optionLabel="shelf_name"
                    placeholder="Search ..."
                    single
                    searchable />
                <x-input label="Stok" wire:model="form.stock" />
                <x-input label="Minimal" wire:model="form.min_stock" />
                <x-radio label="Status Jual" :options="$statuses" option-label="id" wire:model="form.status" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" type="reset" x-on:click="open = false" />
            <x-button label="Save" class="btn-primary" type="submit" />
        </x-slot:actions>
    </x-form>
</x-drawer>
