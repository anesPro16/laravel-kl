<?php

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new 
#[Title('Struk Penjualan PDF')]
#[Layout('components.layouts.empty')]
class extends Component
{
    #[Url]
    public ?int $saleId = null;

    //public Sale $sale;

    public function mount($saleId)
    {
        $sale = Sale::with('items.product', 'user')->findOrFail($this->saleId);

        // Generate PDF dan langsung return response
        // Load HTML dari komponen ini sendiri
        $pdf = Pdf::loadHtml(view('livewire.pdf-struck', compact('sale'))->render())
            ->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm mobile printer size

        return Response::streamDownload(fn () => print($pdf->output()), 'struk.pdf');

    }

    public function export()
    {
        $pdf = Pdf::loadHtml(
            view('livewire.pdf-struck', ['sale' => $this->sale])->render()
        )->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm width in points

        return response()->streamDownload(fn () => print($pdf->output()), 'struk.pdf');
    }

}
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

    {{-- Tombol Ekspor --}}
    <div class="mt-6 flex justify-center">
        <button wire:click="export" class="px-4 py-2 bg-blue-600 text-white text-sm rounded">
            Download PDF
        </button>
    </div>
</div>
