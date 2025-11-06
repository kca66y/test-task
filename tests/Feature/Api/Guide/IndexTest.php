<?php

namespace Tests\Feature\Api\Guide;

use App\Models\Guide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_only_active_guides(): void
    {
        // создаём активных и неактивных гидов
        $activeGuides = Guide::factory()->count(3)->state(['is_active' => true])->create();
        Guide::factory()->count(2)->state(['is_active' => false])->create();

        $response = $this->getJson('/api/v1/guides');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(3, 'data');

        // убеждаемся, что все активные есть в ответе
        foreach ($activeGuides as $guide) {
            $response->assertJsonFragment([
                'id' => $guide->id,
                'name' => $guide->name,
                'is_active' => true,
            ]);
        }
    }

    #[Test]
    public function it_filters_guides_by_min_experience(): void
    {
        // активные гиды с разным опытом
        $junior = Guide::factory()->create(['experience_years' => 2,  'is_active' => true]);
        $middle = Guide::factory()->create(['experience_years' => 5,  'is_active' => true]);
        $senior = Guide::factory()->create(['experience_years' => 10, 'is_active' => true]);

        $response = $this->getJson('/api/v1/guides?min_experience=5');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data');

        // убеждаемся, что младший не попал
        $response->assertJsonMissing(['id' => $junior->id]);
        $response->assertJsonFragment(['id' => $middle->id]);
        $response->assertJsonFragment(['id' => $senior->id]);
    }

    #[Test]
    public function it_returns_empty_when_no_guides_match_filter(): void
    {
        // все активные, но с маленьким опытом
        Guide::factory()->count(3)->create(['experience_years' => 1, 'is_active' => true]);

        $response = $this->getJson('/api/v1/guides?min_experience=10');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_ignores_inactive_guides_even_with_experience(): void
    {
        Guide::factory()->create(['experience_years' => 20, 'is_active' => false]);
        Guide::factory()->create(['experience_years' => 15, 'is_active' => true]);

        $response = $this->getJson('/api/v1/guides?min_experience=10');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonMissing(['is_active' => false]);
    }
}
