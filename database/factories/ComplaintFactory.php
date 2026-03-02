<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'category' => fake()->randomElement(['Pelayanan Publik', 'Infrastruktur', 'Kesehatan', 'Pendidikan', 'Lingkungan', 'Lainnya']),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['baru', 'diproses', 'selesai', 'ditolak']),
            'document' => null,
            'photo' => null,
            'response' => fake()->optional()->paragraph(),
        ];
    }
    
    /**
     * Indicate that the complaint is new.
     */
    public function baru(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'baru',
        ]);
    }
    
    /**
     * Indicate that the complaint is being processed.
     */
    public function diproses(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'diproses',
        ]);
    }
    
    /**
     * Indicate that the complaint is completed.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
        ]);
    }
    
    /**
     * Indicate that the complaint is rejected.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
        ]);
    }
}
