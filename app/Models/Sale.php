<?php

namespace App\Models;

use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
	protected $guarded = [];

  public function items()
	{
	  return $this->hasMany(SaleItem::class);
	}
}
