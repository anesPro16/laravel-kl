<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceIncrease extends Model
{
	protected $guarded = [];


  public function getHargaPersentaseAttribute(): string
  {
      return ($this->price * 100) . '%'; // Konversi 0.30 menjadi "30%"
  }
}
