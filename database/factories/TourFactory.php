<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total_spaces = rand(5, 12);
        $available_spaces = $total_spaces - rand(1, 3);

        return [
            'title' => fake()->title(),
            'description' => fake()->paragraph(1, false),
            'price' => rand(10000, 50000),
            'available_spaces' => $available_spaces,
            'total_spaces' => $total_spaces,
            'date' => fake()->dateTimeBetween(
                now()->subMonths(8),
                now()->addMonths(3)
            )->format('Y-m-d')
        ];
    }
}
