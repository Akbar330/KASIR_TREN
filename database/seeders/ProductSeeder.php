<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Minuman
            ['nama' => 'Aqua 600ml', 'kategori' => 'minuman', 'harga' => 5000, 'stok' => 100, 'barcode' => '8993675010001'],
            ['nama' => 'Pocari Sweat', 'kategori' => 'minuman', 'harga' => 8000, 'stok' => 50, 'barcode' => '8993675020002'],
            ['nama' => 'Teh Botol', 'kategori' => 'minuman', 'harga' => 6000, 'stok' => 75, 'barcode' => '8993675030003'],
            ['nama' => 'Coca Cola', 'kategori' => 'minuman', 'harga' => 7000, 'stok' => 80, 'barcode' => '8993675040004'],
            
            // Makanan
            ['nama' => 'Mie Goreng', 'kategori' => 'makanan', 'harga' => 15000, 'stok' => 30, 'barcode' => '8993675050005'],
            ['nama' => 'Nasi Goreng', 'kategori' => 'makanan', 'harga' => 20000, 'stok' => 25, 'barcode' => '8993675060006'],
            ['nama' => 'Roti Bakar', 'kategori' => 'makanan', 'harga' => 12000, 'stok' => 40, 'barcode' => '8993675070007'],
            
            // Equipment
            ['nama' => 'Kaos Kaki Futsal', 'kategori' => 'equipment', 'harga' => 25000, 'stok' => 20, 'barcode' => '8993675080008'],
            ['nama' => 'Decker', 'kategori' => 'equipment', 'harga' => 15000, 'stok' => 15, 'barcode' => '8993675090009'],
            ['nama' => 'Handuk Kecil', 'kategori' => 'equipment', 'harga' => 10000, 'stok' => 30, 'barcode' => '8993675100010'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
