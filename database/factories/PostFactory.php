<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
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
            'body' => fake()->paragraph(10),
            'image' => 'https://picsum.photos/id/' . $this->faker->numberBetween(1, 50) . '/800/400',
            'slug' => fake()->slug(),
            'published_at' => now(),
        ];
    }
}
