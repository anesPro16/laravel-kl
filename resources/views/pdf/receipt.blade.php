<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .mb-2 { margin-bottom: 6px; }
        .w-100 { width: 100%; }
        .border-top { border-top: 1px dashed #000; margin-top: 10px; padding-top: 6px; }
        .flex-between { display: flex; justify-content: space-between; }
    </style>
</head>
<body>

    <div class="text-center mb-2">
        <strong>Apotek Barkah</strong><br>
        Jl. Masjid Al Barkah No.21
    </div>

    <div class="mb-2">
        <div>No. Struk : STRK-{{ str_pad($sale->receipt, 6, '0', STR_PAD_LEFT) }}</div>
        <div>Kasir     : {{ $sale->user->name ?? '-' }}</div>
        <div>Pelanggan : -</div>
        <div>Tanggal   : {{ $sale->created_at->format('d-m-Y H:i') }}</div>
    </div>

    <div class="border-top">
        @foreach ($sale->items as $item)
            <div>{{ $item->product->product_name }}</div>
            <div class="flex-between">
                <div>{{ $item->quantity }} {{ $item->product->unit }} x {{ number_format($item->price, 0, ',', '.') }}</div>
                <div>{{ number_format($item->subtotal, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>

    <div class="border-top">
        <div class="flex-between">
            <div>Subtotal</div>
            <div>Rp{{ number_format($sale->grand_total, 0, ',', '.') }}</div>
        </div>
        <div class="flex-between">
            <div>Diskon</div>
            <div>
                @if ($sale->discount_type === '%')
                    {{ $sale->discount }}%
                @else
                    Rp{{ number_format($sale->discount, 0, ',', '.') }}
                @endif
            </div>
        </div>
        <div class="flex-between">
            <div>Uang Tunai</div>
            <div>Rp{{ number_format($sale->paid_amount, 0, ',', '.') }}</div>
        </div>
        <div class="flex-between">
            <div>Kembalian</div>
            <div>Rp{{ number_format($sale->change, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="text-center mt-2">
        ~ Terima Kasih ~
    </div>

</body>
</html>
