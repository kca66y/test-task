<?php

namespace Database\Seeders;

use App\Models\Guide;
use Illuminate\Database\Seeder;

class GuideSeeder extends Seeder
{
    public function run(): void
    {
        Guide::factory()->count(5)->active()->state([
            'experience_years' => fn () => fake()->numberBetween(7, 20),
        ])->create();

        Guide::factory()->count(7)->active()->state([
            'experience_years' => fn () => fake()->numberBetween(0, 6),
        ])->create();

        Guide::factory()->count(3)->inactive()->state([
            'experience_years' => fn () => fake()->numberBetween(1, 15),
        ])->create();

        Guide::factory()->create([
            'name' => 'Андрей Сергеев',
            'experience_years' => 12,
            'is_active' => true,
        ]);

        Guide::factory()->create([
            'name' => 'Василий Орлов',
            'experience_years' => 3,
            'is_active' => true,
        ]);
    }
}
