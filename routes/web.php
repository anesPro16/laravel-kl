<?php

use App\Livewire\{
	Cashier,
  Categories,
  CreateFaktur,
  ProductTable,
  PurchaseInvoices,
  RejectList,
  ReturList,
  SalesList,
  Shelves,
  Units,
};
use App\Livewire\Shop\CartDrawer;
use App\Livewire\Shop\Products;
use App\Models\User;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\ReceiptPdfController;

// Volt::route('/', 'users.index');
Route::get('/', ProductTable::class);
Volt::route('/login', 'login')->name('login');
Volt::route('/print-struck/{saleId}', 'print-struck')->name('print');
Volt::route('/sales/receipt-pdf/{saleId}', 'pdf-struck')->name('export-pdf');


Route::get('/sales/{sale}/export-pdf', [ReceiptPdfController::class, 'export'])->name('sales.export.pdf');
Route::get('/invoice/{invoice}/faktur-pdf', [ReceiptPdfController::class, 'fakturPdf']);


/*Route::get('/struk/{sale}/pdf', function (Sale $sale) {
    $pdf = Pdf::loadView('livewire.pdf-struck', compact('sale'))->setPaper([0, 0, 230, 600], 'portrait');
    $filename = 'struk-' . str_pad($sale->receipt, 6, '0', STR_PAD_LEFT) . '.pdf';
    return $pdf->download($filename);
})->name('struk.download');*/

Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
 
    return redirect('/');
});

Route::get('/cart', CartDrawer::class)->middleware('auth');
Route::get('/produk', Products::class)->middleware('auth');
Route::get('/cashier', Cashier::class)->middleware('auth');
Route::get('/faktur', PurchaseInvoices::class)->middleware('auth');
Route::get('/create-faktur', CreateFaktur::class)->middleware('auth');
Route::get('/reject-list', RejectList::class);
Route::get('/retur-list', ReturList::class);
Route::get('/sale-list', SalesList::class);
Route::get('/category', Categories::class);
Route::get('/product', ProductTable::class);
Route::get('/rak', Shelves::class);
Route::get('/unit', Units::class);

Route::get('/get', function () {
  $userId = Auth::id();
  $user = User::findOrFail($userId);
  // $user->update(['name' => 'Seyoh']);
});