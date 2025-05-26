<?php

namespace App\Livewire;

use App\Models\Shelf;
use Livewire\Attributes\Title;

#[Title('Data Rak')]
class Shelves extends BaseCrud
{
	public $title = 'Data Rak';
	
	public string $shelf_name = '';

	protected function getModelClass(): string
	{
		return Shelf::class;
	}

	public string $fieldName = 'shelf_name';
}