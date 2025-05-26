<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoice extends Model
{
  protected $fillable = [
        'supplier_id',
        'no_surat_pesan',
        'no_faktur',
        'tanggal',
        'tgl_penerimaan',
        'jenis_faktur',
        'gudang',
        'jenis_pembayaran',
        'tempo_bayar',
        'jatuh_tempo',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tgl_penerimaan' => 'date',
        'jatuh_tempo' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
