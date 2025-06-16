<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

  public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

  public function getStockAttribute($product)
  {
      return "Stok : {$product}";
  }

  public function getStokAttribute()
  {
    return str_replace("Stok : ", "", $this->stock);
  }

}
