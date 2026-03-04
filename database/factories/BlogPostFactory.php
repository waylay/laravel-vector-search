<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'topic' => fake()->randomElement(['Laravel', 'AI', 'Search', 'Databases']),
            'audience' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(4, true),
            'embedding' => null,
        ];
    }
}
