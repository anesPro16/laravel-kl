<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Title;

#[Title('Data Kategori')]
class Categories extends BaseCrud
{
	public $title = 'Data Kategori';

  public string $category_name = '';

  protected function getModelClass(): string
  {
      return Category::class;
  }

  public string $fieldName = 'category_name';
}