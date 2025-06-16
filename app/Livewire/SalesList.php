<?php 

namespace App\Livewire;

use App\Exports\SalesExport;
use App\Livewire\Traits\HasSalesList;
use Illuminate\Support\Facades\Redirect;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Daftar Penjualan')]
class SalesList extends Component
{
    use HasSalesList;

    public $startDate, $endDate;

    public $title ='Daftar Penjualan';

    /*public function mount()
    {
        dd($this->headers());
    }*/

    public function getSalesProperty()
    {
        // return $this->salesQuery('sold')->latest()->get();
        return $this->salesQuery('sold')->get();
    }

    public function printStruk($saleId)
    {
        return Redirect::route('print', $saleId);
    }

    public function exportExcel()
    {
        // $fileName = 'penjualan_' . now()->format('Ymd_His') . '.xlsx';
        $fileName = 'Daftar Penjualan - ' . now()->translatedFormat('j F Y') . ' pukul ' . now()->format('H.i') . '.xlsx';

        return Excel::download(
            new SalesExport($this->sales, $this->startDate, $this->endDate, $this->title),
            $fileName
        );
    }

    public function render()
    {
        return view('livewire.sales-list', [
            'sales' => $this->salesQuery('sold')->latest()->get(),
            'summary' => $this->salesSummary,
            'date' => $this->dateRangeLabel,
            'headers' => $this->headers(),
        ]);
    }
}
