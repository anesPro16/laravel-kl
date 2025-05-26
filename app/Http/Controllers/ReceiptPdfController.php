<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptPdfController extends Controller
{

  public function export(Request $request, Sale $sale)
	{
    $sale->load('items.product', 'user');

    $mode = $request->query('mode', 'desktop');

    // Pilih ukuran kertas
    $paperSize = $mode === 'mobile'
        //? [58 / 25.4 * 72, 2000] // 58mm lebar → points, tinggi dinamis
        ? [0, 0, 226.77, 439] // 58mm lebar → points, tinggi dinamis
        : 'A5';

    $orientation = $mode === 'mobile' ? 'portrait' : 'landscape';

    // Nama file otomatis
    $filename = 'Struk_' . now()->format('Ymd_His') . '.pdf';

    $pdf = Pdf::loadView('sales.receipt-pdf', [
        'sale' => $sale,
        'mode' => $mode,
    ])->setPaper($paperSize, $orientation);

    // Langsung download dan preview
    return response($pdf->stream($filename))
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
	}
}
