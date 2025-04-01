<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
	protected $guarded = [];

  /*protected $fillable = [
    'product_name',
    'type',
    'product_code',
    'barcode',
    'factory_name',
  ];*/

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
}
