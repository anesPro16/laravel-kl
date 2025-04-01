<?php

use App\Livewire\{
  Categories,
  ProductTable,
  Units,
  // KategoriApp,
};
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'users.index');
Route::get('/category', Categories::class);
Route::get('/product', ProductTable::class);
Route::get('/unit', Units::class);
// Route::get('/kategori', KategoriApp::class);
