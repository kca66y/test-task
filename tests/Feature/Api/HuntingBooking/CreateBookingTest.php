<?php

namespace Tests\Feature\Api\HuntingBooking;

use App\Models\Guide;
use App\Models\HuntingBooking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateBookingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_booking_successfully(): void
    {
        $guide = Guide::factory()->active()->create();

        $payload = [
            'tour_name' => 'Большая охота',
            'hunter_name' => 'Иван Петров',
            'guide_id' => $guide->id,
            'date' => now()->addDays(5)->toDateString(),
            'participants_count' => 3,
        ];

        $response = $this->postJson('/api/v1/bookings', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['status', 'data' => ['id', 'tour_name', 'guide_id']]);

        $this->assertDatabaseHas('hunting_bookings', [
            'guide_id' => $guide->id,
            'tour_name' => 'Большая охота',
        ]);
    }

    #[Test]
    public function it_returns_error_when_guide_is_not_active(): void
    {
        $guide = Guide::factory()->inactive()->create();

        $payload = [
            'tour_name' => 'Тур на болото',
            'hunter_name' => 'Иван',
            'guide_id' => $guide->id,
            'date' => now()->addDay()->toDateString(),
            'participants_count' => 3,
        ];

        $response = $this->postJson('/api/v1/bookings', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['guide_id'])
            ->assertJsonFragment([
                'message' => 'Выбранный гид не найден или неактивен.',
            ]);
    }

    #[Test]
    public function it_returns_error_when_guide_is_busy_on_date(): void
    {
        $guide = Guide::factory()->active()->create();
        HuntingBooking::factory()->create([
            'guide_id' => $guide->id,
            'date' => now()->addDays(3)->toDateString(),
        ]);

        $payload = [
            'tour_name' => 'Повторный тур',
            'hunter_name' => 'Иван',
            'guide_id' => $guide->id,
            'date' => now()->addDays(3)->toDateString(),
            'participants_count' => 2,
        ];

        $response = $this->postJson('/api/v1/bookings', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['guide_id'])
            ->assertJsonFragment([
                'message' => 'Гид уже занят на указанную дату.',
            ]);
    }

    #[Test]
    public function it_returns_error_for_invalid_participants_count(): void
    {
        $guide = Guide::factory()->active()->create();

        $payload = [
            'tour_name' => 'Большая охота',
            'hunter_name' => 'Иван',
            'guide_id' => $guide->id,
            'date' => now()->addDays(2)->toDateString(),
            'participants_count' => 15,
        ];

        $response = $this->postJson('/api/v1/bookings', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['participants_count'])
            ->assertJsonFragment([
                'message' => 'Количество участников должно быть от 1 до 10.',
            ]);
    }

    #[Test]
    public function it_returns_error_for_past_date(): void
    {
        $guide = Guide::factory()->active()->create();

        $payload = [
            'tour_name' => 'Тур в прошлое',
            'hunter_name' => 'Иван',
            'guide_id' => $guide->id,
            'date' => now()->subDay()->toDateString(),
            'participants_count' => 3,
        ];

        $response = $this->postJson('/api/v1/bookings', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date'])
            ->assertJsonFragment([
                'message' => 'Дата тура должна быть не раньше сегодняшнего дня.',
            ]);
    }
}
