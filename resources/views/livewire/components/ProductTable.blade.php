<!-- ProductTable -->
<x-card>
    <x-table :headers="$headers" :rows="$products" :sort-by="$sortBy" with-pagination>
        @scope('cell_index', $product)
            {{ $loop->iteration }}
        @endscope

        @scope('cell_action', $product)
            <div class="flex space-x-2">
                <x-button icon="o-pencil" wire:click="showDrawer('{{ $product->id }}')" class="btn-ghost btn-sm text-blue-500" />
                <x-button icon="o-trash" wire:click="delete('{{ $product->id }}')" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
            </div>
        @endscope
    </x-table>
</x-card>
