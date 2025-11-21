<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('+1 day', '+30 days');
        $endTime = fake()->dateTimeBetween($startTime, (clone $startTime)->modify('+3 hours'));

        return [
            'room_id' => \App\Models\Room::factory(),
            'user_id' => \App\Models\User::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => null,
            'number_of_student' => fake()->numberBetween(1, 50),
            'equipment_needed' => fake()->optional()->sentence(),
            'purpose' => fake()->sentence(),
            'rejection_reason' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => null,
            'rejection_reason' => null,
        ]);
    }
}
