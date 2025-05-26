<?php

namespace App\Livewire;

use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Livewire\Component;
use Illuminate\Support\Collection;

class PurchaseInvoices extends Component
{
    public bool $showingForm = false;
    public bool $editing = false;
    public array $form = [];

    public Collection $suppliers;

    public array $jenis_faktur = [
	    ['id' => 'Tunai'],
	    ['id' => 'Kredit'],
	  ];

	  public array $payments = [
	    ['id' => 'Cash'],
	    ['id' => 'Transfer'],
	  ];

    public function mount()
    {
        $this->suppliers = Supplier::select('id', 'name')->get();
    }

    public function getHeadersProperty(): array
    {
        return [
            ['key' => 'supplier', 'label' => 'Supplier'],
            ['key' => 'no_faktur', 'label' => 'No. Faktur'],
            ['key' => 'tanggal', 'label' => 'Tanggal'],
            ['key' => 'gudang', 'label' => 'Gudang'],
            ['key' => 'action', 'label' => 'Aksi'],
        ];
    }

    public function getInvoicesProperty()
    {
        return PurchaseInvoice::with('supplier')->latest()->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editing = false;
        $this->showingForm = true;
    }

    public function edit(int $id): void
    {
        $invoice = PurchaseInvoice::findOrFail($id);

        $this->form = [
            'id'               => $invoice->id,
            'supplier_id'      => $invoice->supplier_id,
            'no_surat_pesan'   => $invoice->no_surat_pesan,
            'no_faktur'        => $invoice->no_faktur,
            'tanggal'          => $invoice->tanggal->format('Y-m-d'),
            'tgl_penerimaan'   => $invoice->tgl_penerimaan?->format('Y-m-d'),
            'jenis_faktur'     => $invoice->jenis_faktur,
            'gudang'           => $invoice->gudang,
            'jenis_pembayaran' => $invoice->jenis_pembayaran,
            'tempo_bayar'      => $invoice->tempo_bayar,
            'jatuh_tempo'      => $invoice->jatuh_tempo?->format('Y-m-d'),
        ];

        $this->editing = true;
        $this->showingForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'form.supplier_id'      => 'required|exists:suppliers,id',
            'form.no_surat_pesan'   => 'nullable|string|max:255',
            'form.no_faktur'        => 'required|string|max:255',
            'form.tanggal'          => 'required|date',
            'form.tgl_penerimaan'   => 'nullable|date',
            'form.jenis_faktur'     => 'required|in:tunai,kredit',
            'form.gudang'           => 'required|string|max:255',
            'form.jenis_pembayaran' => 'required|in:cash,transfer',
            'form.tempo_bayar'      => 'nullable|integer|min:0',
            'form.jatuh_tempo'      => 'nullable|date',
        ])['form'];

        PurchaseInvoice::updateOrCreate(
            ['id' => $this->form['id'] ?? null],
            $validated
        );

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => $this->editing ? 'Faktur diperbarui' : 'Faktur ditambahkan',
        ]);

        $this->showingForm = false;
    }

    public function resetForm(): void
    {
        $this->reset('form');
    }

    public function render()
    {
        return view('livewire.purchase-invoices', [
            'headers' => $this->headers,
            'jenis_faktur' => $this->jenis_faktur,
            'payments' => $this->payments,
            'invoices' => $this->invoices,
        ]);
    }
}
