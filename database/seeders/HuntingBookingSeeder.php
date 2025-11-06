<?php

namespace Database\Seeders;

use App\Models\Guide;
use App\Models\HuntingBooking;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Random\RandomException;

class HuntingBookingSeeder extends Seeder
{
    public function run(): void
    {
        if (Guide::query()->count() === 0) {
            Guide::factory()->count(8)->active()->create();
        }

        $today = Carbon::today();

        Guide::query()->where('is_active', true)->get()->each(
            /**
             * @throws RandomException
             */ function (Guide $guide) use ($today) {
                $count = random_int(2, 3);

                for ($i = 0; $i < $count; $i++) {
                    $date = $today->copy()->addDays(3 + ($guide->id % 5) + $i);

                    while (
                        HuntingBooking::query()
                            ->where('guide_id', $guide->id)
                            ->whereDate('date', $date->toDateString())
                            ->exists()
                    ) {
                        $date->addDay();
                    }

                    HuntingBooking::factory()->create([
                        'guide_id' => $guide->id,
                        'date' => $date->toDateString(),
                        'participants_count' => random_int(1, 10),
                    ]);
                }
            });

        $demoGuide = Guide::query()->where('is_active', true)->inRandomOrder()->first()
            ?? Guide::factory()->active()->create(['experience_years' => 7, 'name' => 'Демо Гид']);

        HuntingBooking::factory()->create([
            'tour_name' => 'Северный след',
            'hunter_name' => 'Иван Петров',
            'guide_id' => $demoGuide->id,
            'date' => $today->copy()->addDays(10)->toDateString(),
            'participants_count' => 4,
        ]);

        HuntingBooking::factory()->create([
            'tour_name' => 'Ночная тропа',
            'hunter_name' => 'Сергей Кузнецов',
            'guide_id' => $demoGuide->id,
            'date' => $today->copy()->addDays(14)->toDateString(),
            'participants_count' => 2,
        ]);
    }
}
