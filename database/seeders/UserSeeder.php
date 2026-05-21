<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AdminProfile;
use App\Models\InternProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates admin and intern users with their profiles
     */
    public function run(): void
    {
        // 1. Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@anchorprecision.com'],
            [
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        AdminProfile::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'nama' => 'Administrator',
                'no_telp' => '081234567890',
            ]
        );

        // 2. Create Intern Users with profiles
        $interns = [
            [
                'email' => 'ahmad.rizki@email.com',
                'nim_nis' => '2021001',
                'nama_lengkap' => 'Ahmad Rizki Pratama',
                'asal_sekolah_kampus' => 'Universitas Indonesia',
                'no_telp' => '081234567891',
                'nama_pembimbing' => 'Dr. Budi Santoso',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta Selatan',
                'kontak_darurat' => '081234567001',
            ],
            [
                'email' => 'siti.nurhaliza@email.com',
                'nim_nis' => '2021002',
                'nama_lengkap' => 'Siti Nurhaliza',
                'asal_sekolah_kampus' => 'Institut Teknologi Bandung',
                'no_telp' => '081234567892',
                'nama_pembimbing' => 'Prof. Hendra Wijaya',
                'alamat' => 'Jl. Sudirman No. 25, Bandung',
                'kontak_darurat' => '081234567002',
            ],
            [
                'email' => 'fadillah.akbar@email.com',
                'nim_nis' => '2021003',
                'nama_lengkap' => 'Muhammad Fadillah Akbar',
                'asal_sekolah_kampus' => 'Universitas Gadjah Mada',
                'no_telp' => '081234567893',
                'nama_pembimbing' => 'Dr. Rina Susilowati',
                'alamat' => 'Jl. Affandi No. 18, Yogyakarta',
                'kontak_darurat' => '081234567003',
            ],
            [
                'email' => 'dewi.kartika@email.com',
                'nim_nis' => '2021004',
                'nama_lengkap' => 'Dewi Kartika Sari',
                'asal_sekolah_kampus' => 'Universitas Brawijaya',
                'no_telp' => '081234567894',
                'nama_pembimbing' => 'Dr. Agus Prasetyo',
                'alamat' => 'Jl. Soekarno Hatta No. 45, Malang',
                'kontak_darurat' => '081234567004',
            ],
            [
                'email' => 'bagas.prasetyo@email.com',
                'nim_nis' => '2021005',
                'nama_lengkap' => 'Bagas Prasetyo Nugroho',
                'asal_sekolah_kampus' => 'Sekolah Tinggi Teknologi Mandiri',
                'no_telp' => '081234567895',
                'nama_pembimbing' => 'Ir. Joko Susilo',
                'alamat' => 'Jl. Ahmad Yani No. 32, Semarang',
                'kontak_darurat' => '081234567005',
            ],
            [
                'email' => 'anisa.rahma@email.com',
                'nim_nis' => '2021006',
                'nama_lengkap' => 'Anisa Rahma Putri',
                'asal_sekolah_kampus' => 'Universitas Diponegoro',
                'no_telp' => '081234567896',
                'nama_pembimbing' => 'Dr. Nurul Hidayah',
                'alamat' => 'Jl. Pemuda No. 88, Semarang',
                'kontak_darurat' => '081234567006',
            ],
            [
                'email' => 'rizky.ramadhan@email.com',
                'nim_nis' => '2021007',
                'nama_lengkap' => 'Rizky Ramadhan',
                'asal_sekolah_kampus' => 'Politeknik Negeri Jakarta',
                'no_telp' => '081234567897',
                'nama_pembimbing' => 'Dr. Imam Mustofa',
                'alamat' => 'Jl. RE Martadinata No. 15, Depok',
                'kontak_darurat' => '081234567007',
            ],
            [
                'email' => 'putri.melinda@email.com',
                'nim_nis' => '2021008',
                'nama_lengkap' => 'Putri Melinda Saputri',
                'asal_sekolah_kampus' => 'Universitas Padjadjaran',
                'no_telp' => '081234567898',
                'nama_pembimbing' => 'Prof. Dedi Kurniawan',
                'alamat' => 'Jl. Dipati Ukur No. 22, Bandung',
                'kontak_darurat' => '081234567008',
            ],
            [
                'email' => 'yoga.siregar@email.com',
                'nim_nis' => '2021009',
                'nama_lengkap' => 'Yoga Pratama Siregar',
                'asal_sekolah_kampus' => 'Universitas Sumatra Utara',
                'no_telp' => '081234567899',
                'nama_pembimbing' => 'Dr. Herman Sitorus',
                'alamat' => 'Jl. Dr. Mansyur No. 50, Medan',
                'kontak_darurat' => '081234567009',
            ],
            [
                'email' => 'intan.permata@email.com',
                'nim_nis' => '2021010',
                'nama_lengkap' => 'Intan Permatasari',
                'asal_sekolah_kampus' => 'Universitas Airlangga',
                'no_telp' => '081234567890',
                'nama_pembimbing' => 'Dr. Ratna Kumala',
                'alamat' => 'Jl. Dharmahusada No. 35, Surabaya',
                'kontak_darurat' => '081234567010',
            ],
        ];

        foreach ($interns as $intern) {
            $user = User::firstOrCreate(
                ['email' => $intern['email']],
                [
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_INTERN,
                ]
            );

            InternProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nim_nis' => $intern['nim_nis'],
                    'nama_lengkap' => $intern['nama_lengkap'],
                    'asal_sekolah_kampus' => $intern['asal_sekolah_kampus'],
                    'no_telp' => $intern['no_telp'],
                    'nama_pembimbing' => $intern['nama_pembimbing'],
                    'alamat' => $intern['alamat'],
                    'kontak_darurat' => $intern['kontak_darurat'],
                    'tanggal_mulai' => now()->startOfMonth(),
                    'tanggal_selesai' => now()->addMonths(3),
                    'status' => 'aktif',
                ]
            );
        }
    }
}