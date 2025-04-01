<?php
namespace App\Livewire;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
//#[On('categorySaved')]
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class CategoryTable extends Component
{
    use Toast, WithPagination;

    public string $search = '';
    public array $sortBy = ['column' => 'category_name', 'direction' => 'asc'];

    // protected $listeners = ['categorySaved' => '$refresh'];

    
    public function updated($property)
{
    if ($property === 'search' || $property === 'sortBy') {
        $this->resetPage();
    }
}

    #[On('refresh-the-component')]
    public function categories(): LengthAwarePaginator
    {
        return Category::query()
      ->when($this->search, fn(Builder $q) => $q->where('category_name', 'like', "%$this->search%"))
      ->orderBy(...array_values($this->sortBy))
      ->latest('id')
      ->paginate(11);
    }

    public function delete($id)
    {
        Category::find($id)?->delete();
        $this->success('Category deleted successfully', 'Deleted!', position: 'toast-bottom');
    }

    public function render()
    {
        return view('livewire.category-table', [
            'categories' => $this->categories(),
        ]);
    }
}
