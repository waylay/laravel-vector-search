<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductManual>
 */
class ProductManualFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_name' => fake()->randomElement(['VectorPad', 'SignalDesk', 'QueryLens']),
            'version' => fake()->randomElement(['v1.0', 'v1.1', 'v2.0']),
            'section' => fake()->randomElement(['installation', 'configuration', 'troubleshooting']),
            'difficulty' => fake()->randomElement(['basic', 'standard', 'expert']),
            'content' => fake()->paragraphs(3, true),
            'embedding' => null,
        ];
    }
}
