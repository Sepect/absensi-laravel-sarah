<?php

namespace Database\Seeders;

use App\Models\InternProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interns = [
            [
                'nim_nis' => '2021001',
                'nama_lengkap' => 'Ahmad Rizki Pratama',
                'email' => 'ahmad.rizki@email.com',
                'asal_sekolah_kampus' => 'Universitas Indonesia',
                'no_telp' => '081234567891',
                'nama_pembimbing' => 'Dr. Budi Santoso',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta Selatan',
                'kontak_darurat' => '081234567001',
            ],
            [
                'nim_nis' => '2021002',
                'nama_lengkap' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@email.com',
                'asal_sekolah_kampus' => 'Institut Teknologi Bandung',
                'no_telp' => '081234567892',
                'nama_pembimbing' => 'Prof. Hendra Wijaya',
                'alamat' => 'Jl. Sudirman No. 25, Bandung',
                'kontak_darurat' => '081234567002',
            ],
            [
                'nim_nis' => '2021003',
                'nama_lengkap' => 'Muhammad Fadillah Akbar',
                'email' => 'fadillah.akbar@email.com',
                'asal_sekolah_kampus' => 'Universitas Gadjah Mada',
                'no_telp' => '081234567893',
                'nama_pembimbing' => 'Dr. Rina Susilowati',
                'alamat' => 'Jl. Affandi No. 18, Yogyakarta',
                'kontak_darurat' => '081234567003',
            ],
            [
                'nim_nis' => '2021004',
                'nama_lengkap' => 'Dewi Kartika Sari',
                'email' => 'dewi.kartika@email.com',
                'asal_sekolah_kampus' => 'Universitas Brawijaya',
                'no_telp' => '081234567894',
                'nama_pembimbing' => 'Dr. Agus Prasetyo',
                'alamat' => 'Jl. Soekarno Hatta No. 45, Malang',
                'kontak_darurat' => '081234567004',
            ],
            [
                'nim_nis' => '2021005',
                'nama_lengkap' => 'Bagas Prasetyo Nugroho',
                'email' => 'bagas.prasetyo@email.com',
                'asal_sekolah_kampus' => 'Sekolah Tinggi Teknologi Mandiri',
                'no_telp' => '081234567895',
                'nama_pembimbing' => 'Ir. Joko Susilo',
                'alamat' => 'Jl. Ahmad Yani No. 32, Semarang',
                'kontak_darurat' => '081234567005',
            ],
            [
                'nim_nis' => '2021006',
                'nama_lengkap' => 'Anisa Rahma Putri',
                'email' => 'anisa.rahma@email.com',
                'asal_sekolah_kampus' => 'Universitas Diponegoro',
                'no_telp' => '081234567896',
                'nama_pembimbing' => 'Dr. Nurul Hidayah',
                'alamat' => 'Jl. Pemuda No. 88, Semarang',
                'kontak_darurat' => '081234567006',
            ],
            [
                'nim_nis' => '2021007',
                'nama_lengkap' => 'Rizky Ramadhan',
                'email' => 'rizky.ramadhan@email.com',
                'asal_sekolah_kampus' => 'Politeknik Negeri Jakarta',
                'no_telp' => '081234567897',
                'nama_pembimbing' => 'Dr. Imam Mustofa',
                'alamat' => 'Jl. RE Martadinata No. 15, Depok',
                'kontak_darurat' => '081234567007',
            ],
            [
                'nim_nis' => '2021008',
                'nama_lengkap' => 'Putri Melinda Saputri',
                'email' => 'putri.melinda@email.com',
                'asal_sekolah_kampus' => 'Universitas Padjadjaran',
                'no_telp' => '081234567898',
                'nama_pembimbing' => 'Prof. Dedi Kurniawan',
                'alamat' => 'Jl. Dipati Ukur No. 22, Bandung',
                'kontak_darurat' => '081234567008',
            ],
            [
                'nim_nis' => '2021009',
                'nama_lengkap' => 'Yoga Pratama Siregar',
                'email' => 'yoga.siregar@email.com',
                'asal_sekolah_kampus' => 'Universitas Sumatra Utara',
                'no_telp' => '081234567899',
                'nama_pembimbing' => 'Dr. Herman Sitorus',
                'alamat' => 'Jl. Dr. Mansyur No. 50, Medan',
                'kontak_darurat' => '081234567009',
            ],
            [
                'nim_nis' => '2021010',
                'nama_lengkap' => 'Intan Permatasari',
                'email' => 'intan.permata@email.com',
                'asal_sekolah_kampus' => 'Universitas Airlangga',
                'no_telp' => '081234567890',
                'nama_pembimbing' => 'Dr. Ratna Kumala',
                'alamat' => 'Jl. Dharmahusada No. 35, Surabaya',
                'kontak_darurat' => '081234567010',
            ],
        ];

        foreach ($interns as $intern) {
            InternProfile::firstOrCreate(
                ['nim_nis' => $intern['nim_nis']],
                [
                    'nama_lengkap' => $intern['nama_lengkap'],
                    'email' => $intern['email'],
                    'password' => Hash::make('password'),
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
