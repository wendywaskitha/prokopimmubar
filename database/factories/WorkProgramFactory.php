<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkProgram>
 */
class WorkProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['infrastruktur', 'pendidikan', 'kesehatan', 'ekonomi', 'sosial']),
            'status' => fake()->randomElement(['planning', 'ongoing', 'completed', 'delayed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+1 year'),
            'author_id' => User::factory(),
        ];
    }
    
    /**
     * Indicate that the work program is in planning stage.
     */
    public function planning(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planning',
        ]);
    }
    
    /**
     * Indicate that the work program is ongoing.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ongoing',
        ]);
    }
    
    /**
     * Indicate that the work program is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
    
    /**
     * Indicate that the work program is delayed.
     */
    public function delayed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delayed',
        ]);
    }
}
