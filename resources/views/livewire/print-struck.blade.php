<?php

use App\Models\Sale;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new 
#[Title('Struk Penjualan')]
#[Layout('components.layouts.empty')]
class extends Component
{
    public Sale $sale;

    public function mount($saleId)
    {
        $this->sale = Sale::with('items.product', 'user')->findOrFail($saleId);
    }
};
?>

<style>
    body { font-family: monospace; font-size: 14px; padding: 20px; }
    .label-recipt { display: inline-block; width: 100px; font-weight: bold; }
    .line { margin-bottom: 5px; }
</style>

<div>
    <h2 class="text-xl">Apotek Barkah</h2>
    <div>Jl. Masjid Al Barkah No.21</div>

    <div class="line"><span class="label-recipt">No. Struk</span>: {{ 'STRK-' . str_pad($sale->receipt, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="line"><span class="label-recipt">Kasir</span>: {{ $sale->user->name ?? '-' }}</div>
    <div class="line"><span class="label-recipt">Pelanggan</span>: -</div>
    <div class="line"><span class="label-recipt">Tanggal</span>: {{ $sale->created_at->format('d-m-Y H:i') }}</div>

    <hr style="margin: 15px 0;">

    @foreach ($sale->items as $item)
        <div>{{ $item->product->product_name }}</div>
        <div style="display: flex; justify-content: space-between;">
            <span>{{ $item->quantity }} {{ $item->product->unit }} x {{ number_format($item->price, 0, ',', '.') }}</span>
            <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
    @endforeach

    <hr style="margin: 15px 0;">

    <div class="line"><span class="label-recipt">Subtotal</span>: Rp{{ number_format($sale->grand_total, 0, ',', '.') }}</div>

    <div class="line">
        <span class="label-recipt">Diskon</span>: 
        @if ($sale->discount_type === '%')
            {{ $sale->discount }}%
        @else
            Rp{{ number_format($sale->discount, 0, ',', '.') }}
        @endif
    </div>

    <div class="line"><span class="label-recipt">Uang Tunai</span>: Rp{{ number_format($sale->paid_amount, 0, ',', '.') }}</div>
    <div class="line"><span class="label-recipt">Kembalian</span>: Rp{{ number_format($sale->change, 0, ',', '.') }}</div>

    <hr style="margin: 15px 0;">

    <div style="text-align: center;">Terima kasih telah berbelanja!</div>
</div>

<script>
    window.onload = () => window.print();
</script>
