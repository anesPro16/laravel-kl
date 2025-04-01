<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unit_name = ['Box isi 200', 'Botol', 'Kaplet', 'Box isi 50', 'Box isi 100', 'Plester', 'Gulung', 'mg', 'Lusin', 'Kotak', 'Strip', 'Tablet'];
        return [
            'name' => $unit_name[array_rand($unit_name)],
        ];
    }
}
