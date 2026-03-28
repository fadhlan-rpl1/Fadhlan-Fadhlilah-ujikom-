<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama_lengkap' => 'Administrator Parkir',
            'username'     => 'admin',
            'password'     => Hash::make('admin123'),
            'role'         => 'admin',
        ]);

        User::create([
            'nama_lengkap' => 'Petugas Lapangan',
            'username'     => 'petugas',
            'password'     => Hash::make('petugas123'),
            'role'         => 'petugas',
        ]);
        User::create([
            'nama_lengkap' => 'Owner Laporan',
            'username'     => 'owner',
            'password'     => Hash::make('owner123'),
            'role'         => 'owner',
        ]);
    }
}