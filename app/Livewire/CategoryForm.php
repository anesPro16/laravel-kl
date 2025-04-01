<?php
namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Component;
use Mary\Traits\Toast;

class CategoryForm extends Component
{
    use Toast;

    public bool $showModal = false;
    public ?string $categoryId = null;
    public string $categoryName = '';

    protected $listeners = ['openModal' => 'handleOpenModal'];

    protected function rules()
    {
        return [
            'categoryName' => 'required|unique:categories,category_name,' . $this->categoryId,
        ];
    }

    public function handleOpenModal(?string $id = null)
    {
        $this->resetForm();
        $this->resetValidation();
        $this->categoryId = $id;
        if ($id) {
            $this->categoryName = Category::find($id)?->category_name ?? '';
        }
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Category::updateOrCreate(
            ['id' => $this->categoryId ?: Str::ulid()],
            ['category_name' => $this->categoryName]
        );

        $message = $this->categoryId ? 'Category Updated successfully' : 'Category Created successfully';
        $this->success($message, 'Success!', position: 'toast-bottom');

        // $this->dispatch('categorySaved');
        $this->dispatch('refresh-the-component');
        $this->showModal = false;
    }

    public function resetForm()
    {
        $this->categoryId = null;
        $this->categoryName = '';
    }

    public function render()
    {
        return view('livewire.category-form');
    }
}
