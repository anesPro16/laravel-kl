<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shelf>
 */
class ShelfFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shelf_name = ['A1', 'A2', 'A3', 'A4', 'A5', 'A5', 'A6', 'A7', 'B1', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8'];
        return [
            'shelf_name' => $shelf_name[array_rand($shelf_name)],
        ];
    }
}
