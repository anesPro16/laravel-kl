<?php 

namespace App\Livewire;

use Livewire\Component;
use App\Livewire\Traits\HasSalesList;
use Livewire\Attributes\Title;

#[Title('Daftar Penolakan')]
class RejectList extends Component
{
    use HasSalesList;

    public $startDate, $endDate;

    public $title ='Daftar Penolakan';

    public function getSalesProperty()
    {
        return $this->salesQuery('reject')->latest()->get();
    }

    public function rejectHeaders(): array
  {
    return [
      ['key' => 'index', 'label' => '#', 'class' => 'w-1'],
      ['key' => 'tgl_retur', 'label' => 'Tanggal Input', 'class' => 'w-1'],
      ['key' => 'product', 'label' => 'Nama Produk', 'class' => 'w-1'],
      ['key' => 'reason', 'label' => 'Alasan Penolakan', 'class' => 'w-1'],
      ['key' => 'qty', 'label' => 'Kuatitas', 'class' => 'w-1'],
      ['key' => 'grand_total', 'label' => 'Potensi Penjualan', 'class' => 'w-1'],
      ['key' => 'status', 'label' => 'Status', 'class' => 'w-1'],
      ['key' => 'action', 'label' => 'Aksi', 'class' => 'w-10'],
  ];
  }

    public function render()
    {
        return view('livewire.reject-list', [
            'sales' => $this->sales,
            'summary' => $this->salesSummary,
            'date' => $this->dateRangeLabel,
            'headers' => $this->rejectHeaders(),
        ]);
    }
}

