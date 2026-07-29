<?php

namespace Database\Factories;

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'status' => fake()->randomElement(IncidentStatus::cases()),
            'occurred_at' => CarbonImmutable::instance(
                fake()->dateTimeBetween('-30 days', 'now', 'UTC'),
            ),
            'kaiju_id' => Kaiju::factory(),
        ];
    }

    /**
     * Mark the incident as open.
     */
    public function open(): static
    {
        return $this->state(fn () => [
            'status' => IncidentStatus::Open,
        ]);
    }

    /**
     * Mark the incident as contained.
     */
    public function contained(): static
    {
        return $this->state(fn () => [
            'status' => IncidentStatus::Contained,
        ]);
    }

    /**
     * Mark the incident as closed.
     */
    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => IncidentStatus::Closed,
        ]);
    }
}
