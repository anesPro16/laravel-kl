<?php

namespace App\Livewire;

use Livewire\Component;

class FormComponent extends Component
{
    public bool $showModal = false;
    public string $fieldName = '';

    public function mount(string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function save()
    {
        // Simpan data ke database di sini
        $this->dispatch('saved'); // Dispatch event setelah save
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.form-component');
    }
}
