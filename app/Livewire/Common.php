<?php

namespace App\Livewire;

use App\Livewire\Traits\HasSalesList;
use App\Models\Product;
use App\Models\Supplier;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Umum')]
class Common extends Component
{
	use HasSalesList;

  public $startDate, $endDate;

  public $title ='Dashboard Umum';

  public array $rentang = [
      ['name' => 'Hari ini'],
      ['name' => 'Kemarin'],
      ['name' => 'Pekan ini'],
      ['name' => 'Bulan ini'],
      ['name' => 'Bulan lalu'],
  ];

  public function getSalesProperty()
  {
    return $this->salesQuery()->get();
  }

  public function render()
  {
    return view('livewire.common', [
    	'summary' => $this->salesSummary,
      'date' => $this->dateRangeLabel,
      'returCount' => $this->sales->where('status', 'retur')->count(),
      'rejectCount' => $this->sales->where('status', 'reject')->count(),
      'productCount' => Product::all()->count(),
      'supplierCount' => Supplier::all()->count(),
    ]);
  }

}
