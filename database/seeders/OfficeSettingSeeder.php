<?php

namespace Database\Seeders;

use App\Models\OfficeSetting;
use Illuminate\Database\Seeder;

class OfficeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OfficeSetting::updateOrCreate(
            ['id' => 1],
            [
                'nama_instansi' => 'PT. Anchor Precision Indonesia',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'radius_meters' => 100,
                'waktu_masuk' => '08:00:00',
                'waktu_pulang' => '17:00:00',
                'qr_expiry_minutes' => 5,
            ]
        );
    }
}