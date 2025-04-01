<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


abstract class BaseCrud extends Component
{
	use WithPagination, Toast;

    public bool $showModal = false;
    public string $search = '';
    // public string $recordId = '';
    public ?string $recordId = null;
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public Model $model;
    public string $fieldName;

    abstract protected function getModelClass(): string;

    public function mount()
    {
        $this->model = app($this->getModelClass());
    }

    public function headers(): array
    {
        return [
            ['key' => 'index', 'label' => '#', 'class' => 'w-1'],
            ['key' => $this->fieldName, 'label' => ucfirst(str_replace('_', ' ', $this->fieldName)), 'class' => 'w-64'],
        ];
    }

  public function render()
  {
    return view('livewire.base-crud');
  }
}
