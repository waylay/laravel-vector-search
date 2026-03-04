<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupportFaq>
 */
class SupportFaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => fake()->sentence(),
            'answer' => fake()->paragraph(),
            'category' => fake()->randomElement(['billing', 'authentication', 'integration']),
            'product_line' => fake()->randomElement(['core', 'analytics', 'automation']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'embedding' => null,
        ];
    }
}
