<?php

namespace Database\Factories;

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Kaiju>
 */
class KaijuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Str::title(fake()->unique()->word().' '.fake()->unique()->word()),
            'category' => fake()->randomElement(KaijuCategory::cases()),
            'threat_level' => fake()->numberBetween(1, 5),
            'description' => fake()->sentence(),
        ];
    }

    /**
     * Create a kaiju without a description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn () => [
            'description' => null,
        ]);
    }
}
