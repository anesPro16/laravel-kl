<?php

namespace App\Livewire;

use App\Models\Unit;
use Livewire\Attributes\Title;

#[Title('Data Satuan')]
class Units extends BaseCrud
{
	public string $unit_name = '';

    protected function getModelClass(): string
    {
        return Unit::class;
    }

    public string $fieldName = 'unit_name';
}
