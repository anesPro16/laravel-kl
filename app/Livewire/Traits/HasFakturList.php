<?php 

namespace App\Livewire\Traits;

use App\Models\Faktur;
use App\Models\PurchaseInvoice;
use Carbon\Carbon;
use Mary\Traits\Toast;

trait HasFakturList
{
	use Toast;

	// public bool $showingDetail = false;
    // public ?Faktur $selectedFaktur = null;

	public bool $filter = false;

    public string $search = '';
	

    public function headers(): array
    {
        return [
            ['key' => 'index', 'label' => 'No.', 'class' => 'w-1'],
            ['key' => 'tanggal', 'label' => 'Tanggal', 'class' => 'w-1 hidden md:table-cell'],
            ['key' => 'supplier', 'label' => 'Supplier', 'class' => 'w-1'],
            ['key' => 'product', 'label' => 'Produk', 'class' => 'w-1'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-1'],
            ['key' => 'grand_total', 'label' => 'Total Pembelian', 'class' => 'w-1 hidden md:table-cell'],
            ['key' => 'action', 'label' => 'Aksi', 'class' => 'w-1'],
        ];
    }

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

    public function getinvoicesSummaryProperty(): array
    {
        $total = $this->invoices->sum('grand_total');
        $count = $this->invoices->count();

        return [
            'total' => $total,
            'count' => $count,
        ];
    }

    /*public function showDetail($fakturId)
	{
	    $this->selectedFaktur = PurchaseInvoice::with(['faktur.items.product', 'user'])->findOrFail($fakturId);
	    $this->showingDetail = true;
	}*/

    public function clear(): void
    {
        $this->filter = false;
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function retur($invoiceId)
	{
	    $invoice = PurchaseInvoice::findOrFail($invoiceId);
	    $invoice->update(["status" => "retur"]);
	    $this->success('Status berhasil diubah', timeout: 5000);
	}

	public function reject($invoiceId)
	{
	    $invoice = PurchaseInvoice::findOrFail($invoiceId);
	    $invoice->update(["status" => "reject"]);
	    $this->success('Status berhasil diubah', timeout: 5000);
	}
    public function cancel($invoiceId)
    {
        $invoice = PurchaseInvoice::findOrFail($invoiceId);
        $invoice->update(["status" => "process"]);
        $this->success('Status berhasil diubah', timeout: 5000);
    }

    protected function fakturQuery($status = 'process')
    {
        $query = PurchaseInvoice::with('faktur.items.product');

        if ($this->startDate || $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        } else {
            $today = Carbon::today();
                $query->whereDate('created_at', $today);                
        }
        // ⬇️ Tambahkan pencarian berdasarkan nama produk
        if (!empty($this->search)) {
            $query->where(function ($query) {
                $query->whereHas('faktur.items.product', fn($q) =>
                    $q->where('product_name', 'like', '%' . $this->search . '%')
                );
            });
        }


        return $query->where('status', $status);
    }
}
