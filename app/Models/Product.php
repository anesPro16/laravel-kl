<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
	protected $guarded = [];

  public $incrementing = false;
  protected $keyType = 'string';

  // public $timestamps = false;

  protected static function boot()
  {
      parent::boot();
      static::creating(function ($product) {
          $product->id = (string) Str::ulid();
      });
  }

  public function cartItems()
  {
      return $this->hasMany(CartItem::class);
  }

}
