<x-drawer wire:model="showingDetail" with-close-button class="w-full">
    <x-slot name="title">Detail {{ str_replace("Daftar ", "", $title) }}</x-slot>

    @if ($selectedSale)
        <div class="space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                @if ($isReceipt ?? false )
                    <x-label-value label="Waktu Penjualan" :value="$selectedSale->created_at->format('d M Y H:i')" />
                    <x-label-value label="Gudang" value="Default" />
                @else
                    <x-label-value label="Waktu Pengembalian" :value="$selectedSale->updated_at->format('d M Y H:i')" />
                @endif
                <x-label-value label="Petugas" :value="$selectedSale->user->name ?? '-'" />
                <x-label-value label="Total {{ ($isReceipt ?? false) ? 'Penjualan' : 'Pengembalian' }}" :value="'Rp' . number_format($selectedSale->grand_total, 0, ',', '.')" />
            </div>

            <div>
                <h3 class="font-semibold mt-4 mb-2">Barang {{ ($isReceipt ?? false) ? "Terjual" : "Diretur" }}</h3>
                <table class="text-sm w-full">
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>No. Batch</th>
                        <th>Qty</th>
                        <th>Nilai Barang</th>
                        <th>Subtotal</th>
                        @foreach ($selectedSale->items as $index => $item)
                          <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->product_name }}</td>
                            <td>{{ $item->product->product_code }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                          </tr>
                        @endforeach
                        </table>
            </div>

            @if ($isReceipt ?? false )
                {{-- Start Struk --}}
                <div class="mt-6 border-t pt-4 text-sm space-y-1">
                    <strong class="text-xl">Apotek Barkah</strong>
                    <div>Jl. Masjid Al Barkah No.21</div>
                    <div><span class="label-recipt">No. Struk</span>: STRK-{{ str_pad($selectedSale->receipt, 6, '0', STR_PAD_LEFT) }}</div>
                    <div><span class="label-recipt">Kasir</span>: {{ $selectedSale->user->name ?? '-' }}</div>
                    <div><span class="label-recipt">Pelanggan</span>: -</div>
                    <div><span class="label-recipt">Tanggal</span>: {{ $selectedSale->created_at->format('d-m-Y H:i') }}</div>

                    @foreach ($selectedSale->items as $index => $item)
                        <strong>
                            <div>{{ $item->product->product_name }}</div>
                            <div class="flex justify-between w-80">
                                <div>{{ $item->quantity }} {{ $item->product->unit . ' x' }} {{ number_format($item->price, 0, ',', '.') }} </div>
                                <div>{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</div>
                            </div>
                        </strong>
                        @endforeach

                    <div><strong>Subtotal: Rp{{ number_format($selectedSale->grand_total, 0, ',', '.') }}</strong></div>
                    <div><strong>Diskon:
                        @if ($selectedSale->discount_type === '%')
                            {{ $selectedSale->discount }}%
                        @else
                            Rp{{ number_format($selectedSale->discount, 0, ',', '.') }}
                        @endif</strong>
                    </div>
                    <div><strong>Uang Tunai: Rp{{ number_format($selectedSale->paid_amount, 0, ',', '.') }}</strong></div>
                    <div><strong>Kembalian: Rp{{ number_format($selectedSale->change, 0, ',', '.') }}</strong></div>
                </div>
                {{-- End Struk --}}

                <div class="mt-6">
                    <x-button icon="o-printer" wire:click="printStruk({{ $selectedSale->id }})">
                        Cetak Struk
                    </x-button>
                </div>
            @else
                {{ '' }}
                
            @endif

            
            
        </div>
    @endif
</x-drawer>