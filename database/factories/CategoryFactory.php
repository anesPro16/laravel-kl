<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category_name =  [
            'Alkes', 'Analgesik', 'Antibiotik', 'Antihipertensi', 'Antidepresan',
            'Baby shop', 'Batuk', 'Flu dan deman', 'Gigi', 'Herbal', 'Pewangi',
            'Sakit kepala', 'Sirup anak', 'Madu', 'Vitamin'
        ];
        return [
            'name' => $category_name[array_rand($category_name)],
        ];
    }
}
