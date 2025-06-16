<?php

namespace App\Models;

use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
  protected $guarded = [];

  protected $casts = [
			'tanggal'        => 'date',
			'tgl_penerimaan' => 'date',
			'jatuh_tempo'    => 'date',
  ];

  public function items()
	{
	  return $this->hasMany(SaleItem::class);
	}

	public function product()
	{
	    return $this->belongsTo(Product::class);
	}

	public function user()
	{
	  return $this->belongsTo(User::class);
	}

  public function supplier(): BelongsTo
  {
      return $this->belongsTo(Supplier::class);
  }
}
