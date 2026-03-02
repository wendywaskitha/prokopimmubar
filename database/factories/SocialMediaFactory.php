<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialMedia>
 */
class SocialMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => fake()->randomElement(['youtube', 'tiktok', 'facebook', 'instagram', 'twitter']),
            'url' => fake()->url(),
            'embed_code' => fake()->optional()->text(200),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'thumbnail' => fake()->imageUrl(),
        ];
    }
}
