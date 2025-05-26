<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
	protected $guarded = [];

	protected static function boot()
  {
      parent::boot();
      static::creating(function ($sale) {
          $sale->receipt = (string) time();
      });
  }

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

	public function getTglJualAttribute()
  {
    // return date('d M Y H:i', strtotime($this->created_at));
    return $this->created_at->translatedFormat('j F Y  H:i');
  }

  public function getTglReturAttribute()
  {
      return $this->updated_at->translatedFormat('j F Y  H:i');
  }
}
