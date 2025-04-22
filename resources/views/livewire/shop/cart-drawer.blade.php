<div>
	@foreach ($cart->items as $item)
<div class="flex items-center justify-between mb-4">
    <div>
        <p class="font-semibold">{{ $item->product->product_name }}</p>
        <p class="text-sm text-gray-500">Rp {{ number_format($item->price_at_time, 0, ',', '.') }}</p>
    </div>

    <div x-data="{ qty: {{ $item->quantity }} }" class="flex items-center gap-2">
        <button
            x-show="qty > 1"
            x-on:click="$wire.updateQuantity({{ $item->id }}, qty - 1); qty--"
            class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
        >−</button>

        <input
            type="number"
            min="1"
            x-model.number="qty"
            x-on:change="$wire.updateQuantity({{ $item->id }}, qty)"
            class="w-12 text-center border border-gray-300 rounded"
        />

        <button
            x-on:click="$wire.updateQuantity({{ $item->id }}, qty + 1); qty++"
            class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
        >+</button>
    </div>
</div>
@endforeach


</div>
