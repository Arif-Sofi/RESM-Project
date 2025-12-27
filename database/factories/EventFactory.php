<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startAt = fake()->dateTimeBetween('+1 day', '+30 days');
        $endAt = fake()->dateTimeBetween($startAt, (clone $startAt)->modify('+4 hours'));

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'location' => fake()->city() . ' ' . fake()->streetName(),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
