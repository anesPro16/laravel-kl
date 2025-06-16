<?php

namespace App\Models;

use App\Models\FakturItem;
use Illuminate\Database\Eloquent\Model;

class Faktur extends Model
{
  protected $fillable = [
    'user_id', // atribut lain yang ingin diizinkan untuk mass assignment
    'status',
	];

  public function items()
	{
	  return $this->hasMany(FakturItem::class);
	}

}
