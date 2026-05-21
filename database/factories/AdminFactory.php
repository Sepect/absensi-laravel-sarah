<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'no_telp' => fake()->phoneNumber(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the admin is a demo user.
     */
    public function demo(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Administrator',
            'email' => 'admin@anchorprecision.com',
            'password' => Hash::make('password'),
            'no_telp' => '081234567890',
        ]);
    }
}