<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Shelf;
use App\Models\Unit;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Category::factory(7)->create();
        $category_name =  [
            'Alkes', 'Analgesik', 'Antibiotik', 'Antihipertensi', 'Antidepresan',
            'Baby shop', 'Batuk', 'Flu dan deman', 'Gigi', 'Herbal', 'Pewangi',
            'Sakit kepala', 'Sirup anak', 'Madu', 'Vitamin'
        ];

        foreach ($category_name as $category) {
            Category::firstOrCreate(['category_name' => $category]);
        }

        $shelf_name = ['A1', 'A2', 'A3', 'A4', 'A5', 'A5', 'A6', 'A7', 'B1', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8'];

        foreach ($shelf_name as $shelf) {
            Shelf::firstOrCreate(['shelf_name' => $shelf]);
        }

        // Shelf::factory(7)->create();

        // Unit::factory(7)->create();
        $unit_name = ['Box isi 200', 'Botol', 'Kaplet', 'Box isi 50', 'Box isi 100', 'Plester', 'Gulung', 'mg', 'Lusin', 'Kotak', 'Strip', 'Tablet'];

        foreach ($unit_name as $unit) {
            Unit::firstOrCreate(['unit_name' => $unit]);
        }

        $this->call(ProductSeeder::class);
    }
}
