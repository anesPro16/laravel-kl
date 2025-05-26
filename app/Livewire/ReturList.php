<?php 

namespace App\Livewire;

use App\Exports\SalesExport;
use App\Livewire\Traits\HasSalesList;
use Illuminate\Support\Facades\Redirect;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Daftar Pengembalian')]
class ReturList extends Component
{
    use HasSalesList;

    public $startDate, $endDate;

    public $title ='Daftar Pengembalian ';

    public function mount()
    {
        $this->isRetur = true;
    }

    public function getSalesProperty()
    {
        return $this->salesQuery('retur')->get();
    }

    public function exportExcel()
    {
        $fileName = $this->title . now()->translatedFormat('j F Y') . ' pukul ' . now()->format('H.i') . '.xlsx';

        return Excel::download(
            new SalesExport($this->sales, $this->startDate, $this->endDate, $this->title),
            $fileName
        );
    }

    public function render()
    {
        return view('livewire.retur-list', [
            'sales' => $this->salesQuery('retur')->latest()->get(),
            'summary' => $this->salesSummary,
            'date' => $this->dateRangeLabel,
            'headers' => $this->headers(),
        ]);
    }
}

