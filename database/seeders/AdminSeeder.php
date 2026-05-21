<?php

namespace Database\Seeders;

use App\Models\AdminProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminProfile::firstOrCreate(
            ['email' => 'admin@anchorprecision.com'],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('password'),
                'no_telp' => '081234567890',
            ]
        );
    }
}
