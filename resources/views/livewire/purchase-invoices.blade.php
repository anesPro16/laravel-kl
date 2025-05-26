<div>
    <x-card>
        <x-header title="Faktur Pembelian">
            {{-- <x-button label="Tambah Faktur" wire:click="create" /> --}}
        </x-header>
        		<x-button icon="o-pencil" wire:click="create" class="btn-ghost btn-sm text-blue-500" />

        <x-table :headers="$headers" :rows="$invoices">
            @scope('cell_supplier', $invoice)
                {{ $invoice->supplier->name }}
            @endscope

            @scope('cell_no_faktur', $invoice)
                {{ $invoice->no_faktur }}
            @endscope

            @scope('cell_tanggal', $invoice)
                {{ $invoice->tanggal->format('d-m-Y') }}
            @endscope

            @scope('cell_gudang', $invoice)
                {{ $invoice->gudang }}
            @endscope

            @scope('cell_action', $invoice)
            		<x-button label="Edit" icon="o-pencil" wire:click="edit({{ $invoice->id }})" />
                {{-- <x-button.icon  icon="pencil" /> --}}
            @endscope
        </x-table>
    </x-card>

    <x-drawer title="{{ $editing ? 'Edit' : 'Tambah' }} Faktur Pembelian" wire:model="showingForm" with-close-button class="w-full">
        <form wire:submit.prevent="save">
            {{-- <x-input.group label="Supplier">
                <x-select label="Supplier" wire:model.defer="form.supplier_id">
                    <option value="">Pilih Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </x-select>
            </x-input.group> --}}
            <x-choices-offline
                    label="Supplier"
                    wire:model="form.supplier_id"
                    :options="$suppliers"
                    optionValue="id"
                    optionLabel="nama"
                    placeholder="Search ..."
                    single
                    searchable />

                <x-input label="No. Surat Pesan" wire:model.defer="form.no_surat_pesan" />

                <x-input label="No. Faktur" wire:model.defer="form.no_faktur" />

                <x-datetime label="Tanggal Faktur" wire:model.defer="form.tanggal" />

                <x-datetime label="Tanggal Penerimaan" wire:model.defer="form.tgl_penerimaan" />

                <x-select label="Jenis Faktur" wire:model.defer="form.jenis_faktur" :options="$jenis_faktur" optionLabel="id" >
                    {{-- <option value="tunai">Tunai</option>
                    <option value="kredit">Kredit</option> --}}
                </x-select>

            {{-- <x-input.group label="Gudang"> --}}
                <x-input label="Gudang" wire:model.defer="form.gudang" />
            {{-- </x-input.group> --}}

            {{-- <x-input.group label=""> --}}
                <x-select label="Jenis Pembayaran" wire:model.defer="form.jenis_pembayaran" :options="$payments" optionLabel="id">
                </x-select>
            {{-- </x-input.group> --}}

                <x-input label="Tempo Bayar (hari)" type="number" wire:model.defer="form.tempo_bayar" />

            		<x-datetime label="Jatuh Tempo" wire:model.live.debounce.350ms="form.jatuh_tempo" />
                {{-- <x-date-picker wire:model.defer="form.jatuh_tempo" /> --}}

            <x-slot:actions>
                <x-button type="submit" label="Simpan" />
                <x-button label="Reset" icon="o-x-mark" wire:click="clear" />
                {{-- <x-button.text label="Batal" wire:click="$set('showingForm', false)" /> --}}
            </x-slot:actions>
        </form>
    </x-drawer>
</div>