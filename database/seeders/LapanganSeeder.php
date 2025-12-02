<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lapangan;

class LapanganSeeder extends Seeder
{
    public function run()
    {
        $lapangans = [
            [
                'nama' => 'Lapangan A',
                'kategori_lapangs_id' => 1,
                'harga_per_jam' => 150000,
                'status' => 'aktif',
                'keterangan' => 'Lapangan vinyl standar internasional'
            ],
            [
                'nama' => 'Lapangan B',
                'kategori_lapangs_id' => 2,
                'harga_per_jam' => 200000,
                'status' => 'aktif',
                'keterangan' => 'Lapangan rumput sintetis premium'
            ],
            [
                'nama' => 'Lapangan C',
                'kategori_lapangs_id' => 3,
                'harga_per_jam' => 100000,
                'status' => 'aktif',
                'keterangan' => 'Lapangan matras untuk pemula'
            ],
        ];

        foreach ($lapangans as $lapangan) {
            Lapangan::create($lapangan);
        }
    }
}
