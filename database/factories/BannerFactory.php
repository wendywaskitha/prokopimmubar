<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
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
            'image' => fake()->imageUrl(),
            'link' => fake()->optional()->url(),
            'position' => fake()->randomElement(['header', 'sidebar-top', 'sidebar-bottom', 'footer', 'content-top', 'content-bottom', 'opd-head-greeting']),
            'size' => fake()->randomElement(['small', 'medium', 'large', 'responsive', 'card-1x1']),
            'is_active' => fake()->boolean(),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+1 month'),
            'click_count' => fake()->numberBetween(0, 1000),
        ];
    }
    
    /**
     * Indicate that the banner is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
    
    /**
     * Indicate that the banner is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
    
    /**
     * Indicate that the banner is for OPD head greetings.
     */
    public function opdHeadGreeting(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'opd-head-greeting',
            'size' => 'card-1x1',
            'is_active' => true,
        ]);
    }
}
