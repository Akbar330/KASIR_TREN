<?php

namespace Database\Seeders;

use App\Models\KategoriLapang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriLapangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $lapangans = [
            [
                'nama' => 'vinyl',

            ],
            [
                'nama' => 'matras',
 
            ],
            [
                'nama' => 'rumput_sintesis',
              
            ],
        ];

        foreach ($lapangans as $lapangan) {
            KategoriLapang::create($lapangan);
        }
    }
}
