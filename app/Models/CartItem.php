<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
	protected $guarded = [];
	
	public function cart()
	{
	    return $this->belongsTo(Cart::class);
	}

	public function product()
	{
	  return $this->belongsTo(Product::class);
	}

}
