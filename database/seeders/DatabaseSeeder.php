<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\PriceIncrease;
use App\Models\Shelf;
use App\Models\Supplier;
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
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'email' => 'cobacoba',
            'password' => 'cobacoba',
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

        for ($i = 0; $i < 20; ++$i) {
            Supplier::create([
                'nama'    => fake()->company(),
                'alamat'  => fake()->streetAddress(),
                'no_telp' => fake()->phoneNumber(),
                'email'   => fake()->unique()->safeEmail(),
            ]);
        }
        
        $this->call(ProductSeeder::class);
        
        $inventories = [
            ['inventory_name' => 'Etalase'],
            ['inventory_name' => 'Gudanng 1'],
            ['inventory_name' => 'Gudanng 2'],
            ['inventory_name' => 'Gudanng 3'],
            ['inventory_name' => 'Apotek lama'],
        ];

        Inventory::insert($inventories);

        /*$kenaikan = [
            ['type' => 'Klinik', 'price' => 0.15],
            ['type' => 'Bebas', 'price' => 0.2],
            ['type' => 'Resep Bebas', 'price' => 0.3],
            ['type' => 'Resep', 'price' => 0.35],
            ['type' => 'Custom', 'price' => 1],
        ];

        PriceIncrease::insert($kenaikan);*/
    }
}
