<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            ['nama' => 'Budi Santoso', 'no_telp' => '081234567890', 'alamat' => 'Jl. Merdeka No. 1'],
            ['nama' => 'Agus Prasetyo', 'no_telp' => '081234567891', 'alamat' => 'Jl. Sudirman No. 2'],
            ['nama' => 'Rudi Hermawan', 'no_telp' => '081234567892', 'alamat' => 'Jl. Gatot Subroto No. 3'],
            ['nama' => 'Dedi Kurniawan', 'no_telp' => '081234567893', 'alamat' => 'Jl. Ahmad Yani No. 4'],
            ['nama' => 'Eko Wijaya', 'no_telp' => '081234567894', 'alamat' => 'Jl. Diponegoro No. 5'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
