<?php

namespace App\Models;

use App\Models\Faktur;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoice extends Model
{
  protected $fillable = [
        'user_id',
        'supplier_id',
        'faktur_id',
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
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tgl_penerimaan' => 'date',
        'jatuh_tempo' => 'date',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function faktur()
    {
      return $this->belongsTo(Faktur::class);
    }

    public function user()
    {
      return $this->belongsTo(User::class);
    }
}
