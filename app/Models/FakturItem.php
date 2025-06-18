<?php

namespace App\Models;

use App\Models\Faktur;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FakturItem extends Model
{
  protected $guarded = [];
	
	public function faktur()
	{
	    return $this->belongsTo(Faktur::class);
	}

	public function product()
	{
	  return $this->belongsTo(Product::class);
	}

	/*public function getExpDateAttribute()
  {
     return Carbon::parse($this->expired)->format('Y-m-d');
  }*/
}
