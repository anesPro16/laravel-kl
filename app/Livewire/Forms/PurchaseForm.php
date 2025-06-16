<?php

namespace App\Livewire\Forms;

use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\Faktur;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PurchaseForm extends Form
{
	public ?PurchaseInvoice $record;
	public $summary;

  public ?int $user_id = null;

  public ?int $faktur_id = null;

  public string $supplier_id = '';

  public ?int $inventory_id = null;

  public string $no_surat_pesan = '';

  public string $no_faktur = '';

  public string $tanggal = '';

  public string $tgl_penerimaan = '';

  public string $jenis_faktur = '';

  // public ?string $gudang = '';

  public ?string $jenis_pembayaran = '';

  public string $tempo_bayar = '';

  public string $jatuh_tempo = '';

  public int $discount = 0;

  public ?string $status = '';

  public float $ppn = 0;

  public float $grand_total = 0;

  public function getUserFaktur($userId): Faktur
  {
      return Faktur::with('items.product')->firstOrCreate(['user_id' => $userId]);
  }


  public function fillForm(PurchaseInvoice $record = null, $summary = null)
  {
    $this->record = $record;

    $this->user_id = Auth::id();
    $this->supplier_id = $record->supplier_id ?? '';
    $this->inventory_id = $record->inventory_id ?? 1;
    $this->no_surat_pesan = $record->no_surat_pesan ?? '';
    $this->no_faktur = $record->no_faktur ?? '';
    $this->tanggal =  ($record->tanggal) ? $record->tanggal->format('Y-m-d') : now()->format('Y-m-d');
    $this->tgl_penerimaan =  ($record->tgl_penerimaan) ? $record->tgl_penerimaan->format('Y-m-d') : now()->format('Y-m-d');
    $this->jenis_faktur = $record->jenis_faktur ?? 'Harga Belum termasuk Pajak';
    $this->jenis_pembayaran = $record->jenis_pembayaran ?? 'Kredit';
    $this->tempo_bayar = $record->tempo_bayar ?? '0';
    $this->jatuh_tempo =  ($record->jatuh_tempo) ? $record->jatuh_tempo->format('Y-m-d') : now()->format('Y-m-d');

    $faktur = $this->getUserFaktur(Auth::id());
    $item = $faktur->items()->first();
    $this->discount = $record->discount ?? 0;
    $this->status = $record->status ?? 'process';
    $this->ppn = $record->ppn ?? $summary['ppn'];
    $this->grand_total = $record->grand_total ?? $summary['total'];
    // $this->grand_total = $summary['total'] ?? '0';
  }

  public function save($summary = null, $id)
    {
    $this->faktur_id = $id;
    $this->ppn = $summary['ppn'] ?? '0';
    $this->grand_total = $summary['total'] ?? '0';
        $this->validate([
            'user_id' => 'required',
            'faktur_id' => 'required',
            'supplier_id' => 'required|exists:suppliers,id',
            'inventory_id' => 'required',
            'no_surat_pesan' => 'nullable|unique:purchase_invoices,no_surat_pesan,' . $this->record->id,
            'no_faktur' => 'required|unique:purchase_invoices,no_faktur,' . $this->record->id,
            'tanggal' => 'required|date',
            'tgl_penerimaan' => 'nullable|date',
            // 'jenis_faktur' => 'required|in:tunai,kredit',
            'jenis_faktur' => 'required',
            'jenis_pembayaran' => 'required|in:Tunai,Kredit',
            'tempo_bayar' => 'nullable',
            'jatuh_tempo' => 'nullable|date',
            'discount' => 'required',
            'status' => 'required',
            'ppn' => 'required',
            'grand_total' => 'required',
        ]);

        PurchaseInvoice::updateOrCreate(
            ['id' => $this->record->id],
            $this->only([
                'user_id',
                'faktur_id',
                'supplier_id',
                'inventory_id',
                'no_surat_pesan',
                'no_faktur',
                'tanggal',
                'tgl_penerimaan',
                'jenis_faktur',
                'jenis_pembayaran',
                'tempo_bayar',
                'jatuh_tempo',
                'discount',
                'status',
                'ppn',
                'grand_total',
            ])
        );

    }
}
