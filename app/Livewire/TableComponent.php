<?php

namespace App\Livewire;

use App\Livewire\Forms\ComponentForm;
use App\Models\Category;
use App\Models\Unit;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TableComponent extends Component
{
    use Toast, WithPagination;

    public array $headers = [];
    public Model $model;

    public ComponentForm $form;

    public bool $showModal = false;
    // public string $recordId = '';
    public ?string $recordId = null;
    public string $fieldName = '';
    public string $category_name;
    public string $unit_name;
    public string $search = '';
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];

     protected $listeners = ['updateSearch' => 'setSearch'];

    public function mount($headers, Model $model, string $fieldName, string $search, array $sortBy)
    {
        $this->headers = $headers;
        $this->model = $model;
        $this->fieldName = $fieldName;
        $this->search = $search;
        $this->sortBy = $sortBy;
    }

    public function setSearch($value)
    {
        $this->search = $value;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    
    public function records()
    {
        return $this->model::query()
            ->when($this->search, fn($q) => $q->where($this->fieldName, 'like', "%$this->search%"))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(10);
    }

    public function openModal(?string $id = null)
    {
        $this->showModal = true;
        $this->resetValidation();
        $this->resetInput();

        if ($id) {
            $this->recordId = $id;
            if (class_basename(get_class($this->model)) === "Category") {
                $this->fieldName = Category::find($id)?->category_name ?? '';
            } else {
                $this->fieldName = Unit::find($id)?->unit_name ?? '';
            }
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function resetInput()
    {
        $this->recordId = null;
        $this->fieldName = '';
    }

     public function save()
    {
        $fieldData = strtolower(class_basename(get_class($this->model))) . "_name"; 
        // Validasi harus merujuk ke nilai properti langsung
        $this->validate([
            'fieldName' => "required|unique:{$this->model->getTable()},{$fieldData}" . $this->recordId
        ]);

        // Pastikan atribut yang benar digunakan dalam updateOrCreate
        $this->model::updateOrCreate(
            ['id' => $this->recordId ?: Str::ulid()],
            [$fieldData => $this->fieldName] // Ambil dari properti fieldName
        );

        $message = $this->recordId ? 'Updated successfully' : 'Created successfully';
        $this->success($message, 'Success!', position: 'toast-bottom');

        // $this->dispatch('recordUpdated');
        $this->closeModal();
    }
    public function delete($id)
    {
        $this->model::find($id)?->delete();
        $this->warning("Deleted successfully", 'Success!', position: 'toast-bottom');
    }

    public function render()
    {
        return view('livewire.table-component', [
            'records' => $this->records(),
        ]);
    }
}
