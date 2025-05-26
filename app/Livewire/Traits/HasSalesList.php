<?php 

namespace App\Livewire\Traits;

use App\Models\Sale;
use Carbon\Carbon;
use Mary\Traits\Toast;

trait HasSalesList
{
	use Toast;

	public bool $showingDetail = false;
    public ?Sale $selectedSale = null;

	public bool $filter = false;
	public bool $isRetur = false;

    public string $search = '';
	

    public function headers(): array
    {
        return [
            ['key' => 'index', 'label' => 'No.', 'class' => 'w-1'],
        [
            'key' => $this->isRetur ? 'tgl_retur' : 'tgl_jual',
            'label' => $this->isRetur ? 'Tanggal Retur' : 'Tanggal Jual',
            'class' => 'w-1'
        ],
        ['key' => 'receipt', 'label' => 'No. Struk', 'class' => 'w-1 hidden md:table-cell'],
        ['key' => 'product', 'label' => 'Produk', 'class' => 'w-5 hidden xl:table-cell'],
        ['key' => 'status', 'label' => 'Status', 'class' => 'w-5 hidden xl:table-cell'],
        [
            'key' => 'grand_total',
            'label' => $this->isRetur ? 'Total Retur' : 'Total Penjualan',
            'class' => 'w-1 text-right'
        ],
        ['key' => 'action', 'label' => 'Aksi', 'class' => 'w-1 text-center'],
        ];
    }

    /*public function updatedSearch()
    {
        $this->dispatch('updateSearch', $this->search); // ⬅️ KIRIM EVENT
    }*/

    public function setSearch($value)
    {
        $this->search = $value;
    }

    public function getDateRangeLabelProperty(): string
    {
        if ($this->startDate || $this->endDate) {
            $start = Carbon::parse($this->startDate)->translatedFormat('j F Y');
            $end   = Carbon::parse($this->endDate)->translatedFormat('j F Y');
            return "Rekap dari $start s.d. $end";
        }

        $today = now()->translatedFormat('j F Y');
        return "Rekap hari ini ($today)";
    }

    public function getSalesSummaryProperty(): array
    {
        $total = $this->sales->sum('grand_total');
        $count = $this->sales->count();

        return [
            'total' => $total,
            'count' => $count,
        ];
    }

    public function showDetail($saleId)
	{
	    $this->selectedSale = Sale::with(['items.product', 'user'])->findOrFail($saleId);
	    $this->showingDetail = true;
	}

    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function retur($saleId)
	{
	    $sale = Sale::findOrFail($saleId);
	    $sale->update(["status" => "retur"]);
	    $this->success('Status berhasil diubah', timeout: 5000);
	}

	public function reject($saleId)
	{
	    $sale = Sale::findOrFail($saleId);
	    $sale->update(["status" => "reject"]);
	    $this->success('Status berhasil diubah', timeout: 5000);
	}
    public function cancel($saleId)
    {
        $sale = Sale::findOrFail($saleId);
        $sale->update(["status" => "sold"]);
        $this->success('Status berhasil diubah', timeout: 5000);
    }

    protected function salesQuery($status = 'sold')
    {
        $query = Sale::with('items.product');

        if ($this->startDate || $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        } else {
            $today = Carbon::today();
            if ($status == 'sold') {
                $query->whereDate('created_at', $today);                
            } else{
                $query->whereDate('updated_at', $today);
            }
        }
        // ⬇️ Tambahkan pencarian berdasarkan nama produk
        if (!empty($this->search)) {
            $query->where(function ($query) {
                $query->whereHas('items.product', fn($q) =>
                    $q->where('product_name', 'like', '%' . $this->search . '%')
                )
                // ->orWhere('discount', 'like', '%' . $this->search . '%')
                ->orWhere('receipt', 'like', '%' . $this->search . '%');
            });
        }


        return $query->where('status', $status);
    }
}
