<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Title;

#Lazy
#[Title('Data Kategori')]
class Categories extends BaseCrud
{
    public string $category_name = '';

    protected function getModelClass(): string
    {
        return Category::class;
    }

    public string $fieldName = 'category_name';
}
