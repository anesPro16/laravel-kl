<div class="mt-6 border-t pt-4 text-sm space-y-1">
	<p class="text-xl">Apotek Barkah</p>
	<div>Jl. Masjid Al Barkah No.21</div>
	<div><strong>No. Struk:</strong> STRK-{{ str_pad($selectedSale->receipt, 6, '0', STR_PAD_LEFT) }}</div>
	<div><strong>Kasir:</strong> {{ $selectedSale->user->name ?? '-' }}</div>
	<div><strong>Pelanggan:</strong> -</div>
	<div><strong>Tanggal:</strong> {{ $selectedSale->created_at->format('d-m-Y H:i') }}</div>
	<div><strong>Subtotal:</strong> Rp {{ number_format($selectedSale->subtotal, 0, ',', '.') }}</div>
	<div><strong>Diskon:</strong>
		@if ($selectedSale->discount_type === 'percent')
		{{ $selectedSale->discount }}%
		@else
		Rp {{ number_format($selectedSale->discount, 0, ',', '.') }}
		@endif
	</div>
	<div><strong>Uang Tunai:</strong> Rp {{ number_format($selectedSale->paid_amount, 0, ',', '.') }}</div>
	<div><strong>Kembalian:</strong> Rp {{ number_format($selectedSale->change, 0, ',', '.') }}</div>
</div>
{{-- <div class="mt-6">
	<x-button icon="o-printer" wire:click="printStruk({{ $selectedSale->id }})">
		Cetak Struk
	</x-button>
</div> --}}