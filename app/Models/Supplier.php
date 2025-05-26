<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Supplier extends Model
{
  public $incrementing = false; // Nonaktifkan auto-increment ID
  protected $keyType = 'string'; // Gunakan string untuk ULID

  protected static function boot()
  {
      parent::boot();
      static::creating(function ($supplier) {
          $supplier->id = (string) Str::ulid(); // Generate ULID otomatis
      });
  }
}
