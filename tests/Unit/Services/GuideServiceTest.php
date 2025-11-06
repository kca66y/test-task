<?php

namespace Tests\Unit\Services;

use App\DTO\GuideFiltersData;
use App\Models\Guide;
use App\Services\GuideService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuideServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_only_active_guides(): void
    {
        $active = Guide::factory()->count(2)->active()->create();
        Guide::factory()->count(2)->inactive()->create();

        $service = new GuideService;
        $result = $service->getActiveGuides(new GuideFiltersData);

        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn ($g) => $g->is_active === true));
        $this->assertTrue($active->pluck('id')->diff($result->pluck('id'))->isEmpty());
    }

    #[Test]
    public function it_applies_min_experience_filter(): void
    {
        // 2 активных «ветерана» и 3 активных «джуна»
        $v1 = Guide::factory()->active()->create(['experience_years' => 12]);
        $v2 = Guide::factory()->active()->create(['experience_years' => 9]);
        Guide::factory()->count(3)->active()->create(['experience_years' => 2]);

        $service = new GuideService;
        $result = $service->getActiveGuides(new GuideFiltersData(min_experience: 5));

        $ids = $result->pluck('id')->all();

        $this->assertContains($v1->id, $ids);
        $this->assertContains($v2->id, $ids);
        $this->assertTrue($result->every(fn ($g) => $g->experience_years >= 5));
    }
}
