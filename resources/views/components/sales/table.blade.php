<x-table :headers="$headers" :rows="$sales" containerClass>
    @scope('cell_index', $sale)
        {{ $loop->iteration }}
    @endscope
    @if ($isReject ?? false ) {
        @scope('cell_reason', $sale)
            <x-badge value="Stok Tidak Tersedia" class="badge-error text-xs mx-1" />
        @endscope

        @scope('cell_qty', $sale)
            @php
                $items = $sale->items;
                $qty = $items->first()->quantity ?? 0;
                // $productCount = $items->count();
            @endphp
            {{ $qty }}
        @endscope
    }
    @endif


    @scope('cell_product', $sale)
        @php
            $items = $sale->items;
            $firstProduct = $items->first()?->product->product_name ?? '-';
            $qty = $items->first()->quantity ?? 0;
            $unit = $items->first()?->product->unit ?? '-';
            $productCount = $items->count();
        @endphp
        {{ $qty }} {{ $unit . ' x'}} {{ $firstProduct }}
        @if ($productCount > 1)
            <x-badge value="+ {{ $productCount - 1 }} lainnya" class="badge-info text-sm mx-3" />
        @endif
    @endscope

    @if ($isRetur ?? false ) {
        @scope('cell_action', $sale)
        <div class="flex space-x-2">
            <x-button 
                label="Detail" 
                wire:click="showDetail({{ $sale->id }})"
                class="btn-ghost btn-sm text-blue-500" 
                responsive 
            />
            <x-button 
                label="Batalkan"
                wire:click="cancel({{ $sale->id }})"
                class="btn-ghost btn-sm text-red-500" 
                responsive 
            />
        </div>
        @endscope

        } @else {
            @scope('cell_action', $sale)
            <div class="flex space-x-1">
                <x-button 
                label="Detail" 
                wire:click="showDetail({{ $sale->id }})"
                class="btn-ghost btn-sm text-blue-500" 
                responsive 
                />
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-ellipsis-vertical"/>
                    </x-slot:trigger>
                    {{-- <x-menu-item title="Export Pdf" link="{{ route('sales.export.pdf', $sale->id) }}" external /> --}}
                    <x-menu-item title="Export Pdf" onclick="exportPDF({{ $sale->id }})" external />
                    <x-menu-item title="Cetak Faktur" wire:click="printStruk({{ $sale->id }})" />
                    <x-menu-item title="Retur" wire:click="retur({{ $sale->id }})"/>
                    <x-menu-item title="Tolak" wire:click="reject({{ $sale->id }})"/>
                    {{-- <x-menu-item title="Buat ulang" /> --}}
                    {{-- <x-menu-item title="Batalkan" /> --}}
                </x-dropdown>
            </div>
            @endscope
        }
        @endif

</x-table>

<script>
    function exportPDF(id) {
        const isMobile = window.innerWidth <= 768;
        const mode = isMobile ? 'mobile' : 'desktop';
        const url = `/sales/${id}/export-pdf?mode=${mode}`;
        window.open(url, '_blank');
    }
</script>