<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembelian</title>
    <style>
    body {
        font-family: sans-serif;
        font-size: {{ $mode === 'mobile' ? '9px' : '12px' }};
    }
    .label { display: inline-block; width: {{ $mode === 'mobile' ? '84px' : '100px' }}; font-weight: bold; }
    .line { margin-bottom: 2px; }
    .section { margin-bottom: 15px; }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .no-border {
        width: 100%;
        border: hidden;
        margin-top: 10px;
    }
    .table th, .table td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
        font-size: {{ $mode === 'mobile' ? '9px' : '11px' }};
    }
    .hidden{
        display: {{ $mode === 'mobile' ? 'none' : ''  }}
    }
    .font-small{
        font-size: 9px;
        color: gray;
    }
</style>
</head>
<body>
    <h2 style="text-align: center;">Faktur Pembelian</h2>
    <table class="no-border" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td>Supplier </td>
                    <td>: {{ $invoice->supplier->nama }}</td>
                    <td>No. Faktur </td>
                    <td>: {{ str_pad($invoice->no_faktur, 6, '0', STR_PAD_LEFT) }}</td>                    
                </tr>
                <tr>
                    <td>Tanggal Faktur </td>
                    {{-- <td>: {{ $invoice->created_at->format('d-m-Y') }}</td> --}}
                    <td>: {{ $invoice->created_at->translatedFormat('j F Y') }}</td>
                    <td>Jenis Pembayaran </td>
                    <td>: {{ $invoice->jenis_pembayaran }}</td>
                </tr>
                <tr>
                    <td>Jatuh Tempo </td>
                    <td>: {{ $invoice->jatuh_tempo->translatedFormat('j F Y') }}</td>                    
                    <td>No. Surat Pesanan </td>
                    <td>: {{ str_pad($invoice->no_surat_pesan, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td>Tanggal Penerimaan </td>
                    <td>: {{ $invoice->tgl_penerimaan->translatedFormat('j F Y H:i') }}</td>                    
                    <td>Gudang Penerima </td>
                    <td>: {{ $invoice->inventory->inventory_name }}</td>
                </tr>
                <tr>
                    <td>Jenis Faktur </td>
                    <td>: {{ $invoice->jenis_faktur }}</td>                    
                </tr>
            </tbody>
        </table>
    {{-- <div class="flex">
        <div>
            <div class="line"><span class="label">Supplier</span>: {{ $invoice->supplier->nama }}</div>
            <div class="line"><span class="label">No. Faktur</span>: {{ $invoice->no_faktur ?? '-' }}</div>
            <div class="line"><span class="label">Tanggal Faktur</span>: {{ $invoice->created_at->format('d-m-Y H:i') }}</div>
            <div class="line"><span class="label">Jenis Pembayaran</span>: {{ $invoice->jenis_pembayaran }} </div>
            <div class="line"><span class="label">Jatuh Tempo</span>: {{ $invoice->jatuh_tempo }} </div>
        </div>
        <div>
            <div class="line"><span class="label">No. Surat Pesanan</span>: {{ $invoice->no_surat_pesan }}</div>
            <div class="line"><span class="label">Tanggal Penerimaan</span>: {{ $invoice->tgl_penerimaan->format('d-m-Y H:i') }}</div>
            <div class="line"><span class="label">Gudang Penerima</span>: {{ $invoice->inventory->inventory_name ?? '-' }}</div>
            <div class="line"><span class="label">Jenis Faktur</span>: {{ $invoice->jenis_faktur }} </div>
        </div>
    </div>
 --}}
    <div class="section">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Exp. date</th>
                    <th class="hidden">No. Batch</th>
                    <th>Qty</th>
                    <th>Harga Beli</th>
                    <th class="hidden">Diskon</th>
                    <th class="hidden">Pajak</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->faktur->items as $index => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->product->product_name }}</td>
                    {{-- {{ dd($item) }} --}}
                    <td>{{ $item->expired }}</td>
                    <td class="hidden">{{ $item->product->product_code }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="hidden">{{ $item->discount }}%</td>
                    <td class="hidden">{{ $item->tax }}%</td>
                    <td>Rp{{ number_format( round($item->quantity * $item->primary_price) , 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="line"><span class="label">Subtotal</span>: Rp{{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
    <div class="line"><span class="label">Diskon Faktur</span>: 
            {{ $invoice->discount }}%
    </div>
    <div class="line"><span class="label">PPN</span>: Rp{{ number_format($invoice->ppn, 0, ',', '.') }}</div>
    <div class="line"><span class="label">Total</span>: Rp{{ number_format($invoice->grand_total, 0, ',', '.') }}</div>

    <hr>
    <div class="font-small">Diinput pada pada {{now()->translatedFormat('j F Y') . ' pukul ' . now()->format('H:i')}} oleh {{$invoice->user->name}} <br>
    APOTEK Barkah
</div>
</body>
</html>
