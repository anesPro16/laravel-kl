<?php

use App\Livewire\{
	Cashier,
  Categories,
  ProductTable,
  Units,
};
use App\Livewire\Shop\CartDrawer;
use App\Livewire\Shop\Products;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'users.index');
Volt::route('/login', 'login')->name('login');

Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
 
    return redirect('/');
});

Route::get('/cart', CartDrawer::class)->middleware('auth');
Route::get('/produk', Products::class)->middleware('auth');
Route::get('/cashier', Cashier::class);
Route::get('/category', Categories::class);
Route::get('/product', ProductTable::class);
Route::get('/unit', Units::class);
