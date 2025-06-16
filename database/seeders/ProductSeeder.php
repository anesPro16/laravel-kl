<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shelf;
use App\Models\Supplier;
use App\Models\Unit;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    		$obatList = [
            'Paracetamol', 'Ibuprofen', 'Amoxicillin', 'Ciprofloxacin', 'Metformin',
            'Omeprazole', 'Atorvastatin', 'Losartan', 'Amlodipine', 'Simvastatin',
            'Lisinopril', 'Clopidogrel', 'Cetirizine', 'Furosemide', 'Metoprolol',
            'Salbutamol', 'Ranitidine', 'Prednisolone', 'Levothyroxine', 'Fluconazole',
            'Azithromycin', 'Doxycycline', 'Tramadol', 'Diclofenac', 'Acetaminophen',
            'Hydrochlorothiazide', 'Propranolol', 'Captopril', 'Nitroglycerin', 'Budesonide',
            'Lorazepam', 'Diazepam', 'Alprazolam', 'Warfarin', 'Rosuvastatin', 'Citalopram',
            'Sertraline', 'Esomeprazole', 'Pantoprazole', 'Miconazole', 'Clotrimazole',
            'Ketoconazole', 'Hydrocortisone', 'Mometasone', 'Betamethasone', 'Folic Acid',
            'Vitamin D3', 'Vitamin C', 'Aspirin', 'Carbamazepine', 'Gabapentin'
        ];
        $tipeList = ['Alkes', 'Jasa', 'Obat', 'Umum'];

        for ($i = 0; $i < 24; ++$i) {
            $nama = $obatList[array_rand($obatList)];
            $tipe = $tipeList[array_rand($tipeList)];
            $barcode = fake()->ean13();
            $hargaBeli = rand(5000, 50000);
            $hargaJual = $hargaBeli + rand(5000, 20000);
            $stok = rand(10, 100);
            $stokMin = rand(1, 10);

            // Generate kode produk
            $kodeProduk = $this->generateKodeProduk($tipe, $nama);

            Product::firstOrCreate([
                'product_name' => $nama,
                'type' => $tipe,
                'product_code' => $kodeProduk,
                'barcode' => $barcode,
                'supplier_id' => Supplier::inRandomOrder()->first()->id,
                'unit' => Unit::inRandomOrder()->first()->unit_name,
                'purchase_price' => $hargaBeli,
                'selling_price' => $hargaJual,
                'category' => Category::inRandomOrder()->first()->category_name,
                'shelf' => Shelf::inRandomOrder()->first()->shelf_name,
                'stock' => $stok,
                'min_stock' => $stokMin,
                // 'status' => 'yes',
            ]);
        }
    }

    private function generateKodeProduk(string $tipe, ?string $nama): string
    {
        // Jika nama kosong, buat default: Tipe-00001
        if (empty($nama)) {
            return strtoupper(substr($tipe, 0, 2)) . '-00001';
        }

        // Ambil 2 huruf pertama tipe & 3 huruf pertama nama
        $kode = strtoupper(substr($tipe, 0, 2)) . '-' . strtoupper(substr($nama, 0, 3));

        // Cek apakah kode sudah ada di database
        $lastProduk = Product::where('product_code', 'like', $kode . '%')->orderBy('product_code', 'desc')->first();

        if ($lastProduk) {
            // Ambil angka terakhir dan tambahkan 1
            preg_match('/\d+$/', $lastProduk->kode_produk, $matches);
            $increment = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
        } else {
            $increment = 1;
        }

        return $kode . '-' . $increment;
    }
}
