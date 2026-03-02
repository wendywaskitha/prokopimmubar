<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();
        $slug = Str::slug($title);
        
        return [
            'title' => $title,
            'slug' => $slug,
            'content' => fake()->realText(2000),
            'excerpt' => fake()->sentence(),
            'featured_image' => fake()->imageUrl(),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'author_id' => User::factory(),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
    
    /**
     * Indicate that the news is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
    
    /**
     * Indicate that the news is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
    
    /**
     * Indicate that the news is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
            'published_at' => fake()->dateTimeBetween('-2 years', '-1 year'),
        ]);
    }
}
