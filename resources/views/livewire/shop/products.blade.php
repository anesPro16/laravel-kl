<div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach ($products as $product)
        <div class="border p-4 rounded-lg shadow-sm">
            <h3 class="font-bold">{{ $product->product_name }}</h3>
            <p class="text-sm text-gray-500">Rp {{ number_format($product->selling_price) }}</p>
            <button 
                wire:click="addToCart('{{ $product->id }}')"
                class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
                Add to Cart
            </button>
        </div>
    @endforeach
</div>

</div>
