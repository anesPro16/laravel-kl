<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shelf extends Model
{
    /** @use HasFactory<\Database\Factories\ShelfFactory> */
    use HasFactory;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($shelf) {
            $shelf->id = (string) Str::ulid();
        });
    }
}
