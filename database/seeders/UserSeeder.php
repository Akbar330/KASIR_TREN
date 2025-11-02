<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@futsal.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1
        ]);

        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@futsal.com',
            'password' => Hash::make('kasir123'),
            'role_id' => 2
        ]);
    }
}