<?php

namespace Database\Factories;

use App\Models\OfficeSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfficeSetting>
 */
class OfficeSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OfficeSetting::class;

    /**
     * Indonesian company names
     */
    protected array $companyNames = [
        'PT. Anchor Precision Indonesia',
        'PT. Maju Bersama Teknologi',
        'PT. Cerdas Digital Indonesia',
        'PT. Inovasi Sistem Terapan',
        'PT. Global Teknologi Nusantara',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_instansi' => $this->companyNames[array_rand($this->companyNames)],
            'latitude' => fake()->latitude(-6.5, -5.5),
            'longitude' => fake()->longitude(106.3, 107.5),
            'radius_meters' => fake()->randomElement([50, 100, 150, 200]),
            'waktu_masuk' => fake()->randomElement(['07:00:00', '08:00:00', '09:00:00']),
            'waktu_pulang' => fake()->randomElement(['16:00:00', '17:00:00', '18:00:00']),
            'qr_expiry_minutes' => fake()->randomElement([3, 5, 10, 15]),
        ];
    }

    /**
     * Create anchor precision office settings.
     */
    public function anchorPrecision(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_instansi' => 'PT. Anchor Precision Indonesia',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meters' => 100,
            'waktu_masuk' => '08:00:00',
            'waktu_pulang' => '17:00:00',
            'qr_expiry_minutes' => 5,
        ]);
    }

    /**
     * Create with custom radius.
     */
    public function withRadius(int $radius): static
    {
        return $this->state(fn (array $attributes) => [
            'radius_meters' => $radius,
        ]);
    }

    /**
     * Create with flexible hours.
     */
    public function flexibleHours(): static
    {
        return $this->state(fn (array $attributes) => [
            'waktu_masuk' => '09:00:00',
            'waktu_pulang' => '18:00:00',
        ]);
    }
}