<?php

namespace Database\Factories;

use App\Models\Guide;
use App\Models\HuntingBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HuntingBooking>
 */
class HuntingBookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tour_name' => $this->faker->randomElement([
                'Лосиный бор', 'Северный след', 'Тихая заимка', 'Сокол и степь', 'Тропа охотника',
            ]),
            'hunter_name' => $this->faker->name(),
            'guide_id' => Guide::factory()->active(),
            'date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'participants_count' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function ensureUniqueDate(): self
    {
        return $this->state(function () {
            static $offset = 0;
            $offset++;

            return ['date' => now()->startOfDay()->addDays($offset)->toDateString()];
        });
    }
}
