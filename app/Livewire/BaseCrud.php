<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


abstract class BaseCrud extends Component
{
    public string $fieldName;
    public Model $model;
    public string $search = '';
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];

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
            ['key' => 'actions', 'label' => 'Aksi', 'class' => 'w-1 text-center'],
        ];
    }

    public function updatedSearch()
    {
        $this->dispatch('updateSearch', $this->search); // ⬅️ KIRIM EVENT
    }

    public function render()
    {
        return view('livewire.base-crud', [
            'search' => $this->search,
        ]);
    }
}
