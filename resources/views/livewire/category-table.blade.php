<x-card>
	<x-header title="Category" separator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce.350ms="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <x-table :headers="[['key' => 'index', 'label' => '#'], ['key' => 'category_name', 'label' => 'Category Name']]" 
        :rows="$categories"
        with-pagination>
        
        @foreach ($categories as $category)
            @scope('cell_index', $category)
                {{ $loop->iteration }}
            @endscope
            
            @scope('actions', $category)
            <div class="flex space-x-2">
                <x-button icon="o-pencil-square" x-on:click="$dispatch('openModal', { id: '{{ $category->id }}' })" class="btn-ghost btn-sm text-blue-500" />
                <x-button icon="o-trash" wire:click="delete('{{ $category->id }}')" wire:confirm="Are you sure?" class="btn-ghost btn-sm text-red-500" />
            </div>
            @endscope
        @endforeach

    </x-table>
    {{ $categories->links() }}
</x-card>
