<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class SalesExport implements FromArray, WithEvents, ShouldAutoSize
{
	protected $sales, $startDate, $endDate, $title;

	public function __construct($sales, $startDate, $endDate, $title)
	{
		$this->sales = $sales;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->title = $title;
	}

	public function array(): array
	{
		$rows = [];
    // Header
		$rows[] = ["$this->title Klinik Barkah"];
		$rows[] = ['--------------------------'];
		$rows[] = ['Dari Tanggal', ': ' . Carbon::parse($this->startDate)->translatedFormat('j F Y')];
		$rows[] = ['Sampai Tanggal', ': ' . Carbon::parse($this->endDate)->translatedFormat('j F Y')];
		$rows[] = ['Di-export Pada Tanggal', ': ' . now()->translatedFormat('j F Y') . ' pukul ' . now()->format('H:i')];
		$rows[] = ['--------------------------'];
    $rows[] = []; // Empty row

    // Table header
    $rows[] = [
    	'No', 'Kode Struk', 'Tanggal', 'Nama Produk', 'Jumlah', 'Satuan',
    	'Harga Jual', 'Sub-total' , 'Diskon', 'Total',
    	'Metode Pembayaran', 'Kembalian', 'Kasir'
    ];

    $no = 1;
    foreach ($this->sales as $sale) {
    	$first = true;
    	foreach ($sale->items as $item) {
    		$row = [];

    		if ($first) {
          $row[] = $no++; // No
          $row[] = "STRK-" . str_pad($sale->receipt, 6, '0', STR_PAD_LEFT);
          $row[] = $sale->created_at->translatedFormat('j F Y  H:i');
        } else {
          $row[] = $row[] = $row[] = ''; // Empty No, Kode, Tanggal
        }

        // $row[] = $item->product_name;
        $row[] = $item->product->product_name;
        $row[] = $item->quantity;
        $row[] = $item->product->unit;
        $row[] = $item->price;
        // $row[] = $sale->discount ?? '';
        $row[] = $item->subtotal;
				
		// dd();

        if ($first) {
        	// $row[] = $sale->discount . $sale->discount_type ?? 0;
        	$row[] = ($sale->discount_type === '%') ? $sale->discount . '%' : 'Rp' . $sale->discount?? '0';
        	$row[] = $sale->grand_total;
        	$row[] = $sale->paid_methods . ' (' . $sale->paid_amount . ')';
        	$row[] = ($sale->change == 0) ? '0' : $sale->change;
        	$row[] = $sale->user->name;
        } else {
          $row[] = $row[] = $row[] = ''; // Kosongkan kolom lainnya
        }

        $rows[] = $row;
        $first = false;
      }
    }

    return $rows;
  }

  public function registerEvents(): array
  {
    return [
        AfterSheet::class => function (AfterSheet $event) {
            // Wrap text untuk judul dan isi
            $event->sheet->getDelegate()->getStyle('A1:M1000')->getAlignment()->setWrapText(true);
            // Auto size handled by ShouldAutoSize
        },
    ];
  }
  
}