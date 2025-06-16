<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class InvoiceExport implements FromArray, WithEvents, ShouldAutoSize
{
	protected $invoices, $startDate, $endDate, $title;

	public function __construct($invoices, $startDate, $endDate, $title)
	{
		$this->invoices = $invoices;
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
    	'No', 'Tanggal', 'No. Faktur', 'No. Pesanan', 'Tanggal Faktur', 'Waktu Penerimaan', 'Supplier', 'Kode Produk', 'Nama Produk', 'ED', 'Kuantitas', 'Satuan',
    	'Harga', 'Diskon Satuan', 'Pajak (%)', 'Subtotal' , 'Pembayaran', 'Jatuh Tempo', 'Diskon Faktur', 'Pajak',
    	'Total Pembelian'
    ];

    $no = 1;
    foreach ($this->invoices as $invoice) {
    	$first = true;
    	foreach ($invoice->faktur->items as $item) {
    		$row = [];

    		if ($first) {
          $row[] = $no++; // No
          $row[] = $invoice->created_at->translatedFormat('j F Y  H:i');
          $row[] = str_pad($invoice->no_faktur, 6, '0', STR_PAD_LEFT);
          $row[] = str_pad($invoice->no_surat_pesan, 6, '0', STR_PAD_LEFT);
          $row[] = $invoice->tanggal->translatedFormat('j F Y');
          $row[] = $invoice->tgl_penerimaan->translatedFormat('j F Y  H:i');
          $row[] = $invoice->inventory->inventory_name ?? '-';
        } else {
          $row[] = $row[] = $row[] = $row[] = $row[] = $row[] = $row[] = ''; // Empty No, Kode, Tanggal
        }

        // $row[] = $item->product_name;
        $row[] = $item->product->product_name;
        $row[] = $item->product->product_code;
        $row[] = $item->expired;
        $row[] = $item->quantity;
        $row[] = $item->product->unit;
        $row[] = $item->primary_price;
        $row[] = $item->discount ?? '0';
        $row[] = $item->tax ?? '0';
        $row[] = $item->quantity * $item->primary_price;
				
		// dd();

        if ($first) {
        	// $row[] = $invoice->discount . $invoice->discount_type ?? 0;
        	// $row[] = ($invoice->discount_type === '%') ? $invoice->discount . '%' : 'Rp' . $invoice->discount?? '0';
        	$row[] = $invoice->jenis_pembayaran;
            $row[] = $invoice->jatuh_tempo;
            $row[] = $invoice->discount;
            $row[] = $invoice->ppn;
            $row[] = $invoice->grand_total;
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