<?php

namespace Database\Factories;

use App\Models\Intern;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intern>
 */
class InternFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Intern::class;

    /**
     * Indonesian first names
     */
    protected array $indonesianFirstNames = [
        'Ahmad', 'Siti', 'Muhammad', 'Dewi', 'Bagas', 'Anisa', 'Rizky', 'Putri',
        'Yoga', 'Intan', 'Budi', 'Sari', 'Wati', 'Hadi', 'Lestari', 'Pratama',
        'Kurniawan', 'Rahayu', 'Saputra', 'Wulandari', 'Hidayat', 'Nugroho',
        'Permana', 'Santoso', 'Susilowati', 'Prasetyo', 'Kusuma', 'Andriani',
    ];

    /**
     * Indonesian last names
     */
    protected array $indonesianLastNames = [
        'Pratama', 'Santoso', 'Wijaya', 'Susilowati', 'Prasetyo', 'Nugroho',
        'Kurniawan', 'Saputra', 'Wulandari', 'Hidayat', 'Kusuma', 'Andriani',
        'Rahayu', 'Lestari', 'Wati', 'Permana', 'Putra', 'Sari', 'Hadi',
    ];

    /**
     * Indonesian universities
     */
    protected array $universities = [
        'Universitas Indonesia',
        'Institut Teknologi Bandung',
        'Universitas Gadjah Mada',
        'Universitas Brawijaya',
        'Sekolah Tinggi Teknologi Mandiri',
        'Universitas Diponegoro',
        'Politeknik Negeri Jakarta',
        'Universitas Padjadjaran',
        'Universitas Sumatra Utara',
        'Universitas Airlangga',
        'Universitas Hassanuddin',
        'Universitas Diponegoro',
        'Institut Pertanian Bogor',
        'Universitas Pendidikan Indonesia',
        'Universitas Sebelas Maret',
    ];

    /**
     * Indonesian cities
     */
    protected array $cities = [
        'Jakarta', 'Bandung', 'Yogyakarta', 'Semarang', 'Surabaya', 'Medan',
        'Makassar', 'Palembang', 'Depok', 'Tangerang', 'Bekasi', 'Malang',
        'Solo', 'Bogor', 'Denpasar', 'Pontianak', 'Banjarmasin', 'Manado',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->indonesianFirstNames[array_rand($this->indonesianFirstNames)];
        $lastName = $this->indonesianLastNames[array_rand($this->indonesianLastNames)];
        $fullName = "$firstName $lastName";

        return [
            'nim_nis' => fake()->unique()->numerify('2021###'),
            'nama_lengkap' => $fullName,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'asal_sekolah_kampus' => $this->universities[array_rand($this->universities)],
            'no_telp' => fake()->phoneNumber(),
            'nama_pembimbing' => 'Dr. ' . fake()->name(),
            'alamat' => 'Jl. ' . fake()->streetName() . ' No. ' . fake()->numberBetween(1, 100) . ', ' . $this->cities[array_rand($this->cities)],
            'kontak_darurat' => fake()->phoneNumber(),
            'tanggal_mulai' => now()->startOfMonth(),
            'tanggal_selesai' => now()->addMonths(fake()->numberBetween(1, 6)),
            'status' => 'aktif',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the intern is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'nonaktif',
        ]);
    }

    /**
     * Indicate that the intern has completed their internship.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
            'tanggal_selesai' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }
}