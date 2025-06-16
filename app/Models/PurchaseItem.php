<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
	protected $guarded = [];
	
  public function sale()
	{
	    return $this->belongsTo(Sale::class);
	}

  public function product()
	{
	    return $this->belongsTo(Product::class);
	}
}
