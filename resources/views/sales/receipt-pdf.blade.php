<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan</title>
    <style>
    body {
        font-family: sans-serif;
        font-size: {{ $mode === 'mobile' ? '9px' : '12px' }};
    }
    .label { display: inline-block; width: 100px; font-weight: bold; }
    .line { margin-bottom: 5px; }
    .section { margin-bottom: 15px; }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .table th, .table td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
        font-size: {{ $mode === 'mobile' ? '9px' : '11px' }};
    }
</style>
</head>
<body>
    <h2 style="text-align: center;">Apotek Barkah</h2>
    <div class="line"><span class="label">No. Struk</span>: STRK-{{ str_pad($sale->receipt, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="line"><span class="label">Kasir</span>: {{ $sale->user->name ?? '-' }}</div>
    <div class="line"><span class="label">Pelanggan</span>: -</div>
    <div class="line"><span class="label">Tanggal</span>: {{ $sale->created_at->format('d-m-Y H:i') }}</div>

    <div class="section">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>No. Batch</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $index => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->product->product_name }}</td>
                    <td>{{ $item->product->product_code }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="line"><span class="label">Subtotal</span>: Rp{{ number_format($sale->grand_total, 0, ',', '.') }}</div>
    <div class="line"><span class="label">Diskon</span>: 
        @if ($sale->discount_type === '%')
            {{ $sale->discount }}%
        @else
            Rp{{ number_format($sale->discount, 0, ',', '.') }}
        @endif
    </div>
    <div class="line"><span class="label">Uang Tunai</span>: Rp{{ number_format($sale->paid_amount, 0, ',', '.') }}</div>
    <div class="line"><span class="label">Kembalian</span>: Rp{{ number_format($sale->change, 0, ',', '.') }}</div>

    <hr>
    <div style="text-align: center;">Terima kasih telah berbelanja!</div>
</body>
</html>
