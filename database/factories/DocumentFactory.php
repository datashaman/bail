<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraphs(3, true),
            'meta' => [
                'author' => $this->faker->name(),
                'created_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            ],
        ];
    }
}
