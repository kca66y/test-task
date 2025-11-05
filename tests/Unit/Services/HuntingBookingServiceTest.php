<?php

namespace Tests\Unit\Services;

use App\DTO\HuntingBookingData;
use App\Exceptions\HuntingBooking\GuideBusyException;
use App\Models\Guide;
use App\Models\HuntingBooking;
use App\Services\HuntingBookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HuntingBookingServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_booking_when_guide_is_active_and_free(): void
    {
        $guide = Guide::factory()->active()->create();

        $dto = new HuntingBookingData(
            tour_name: 'Большая охота',
            hunter_name: 'Иван Петров',
            guide_id: $guide->id,
            date: now()->addDays(3),
            participants_count: 4,
        );

        $service = new HuntingBookingService;
        $booking = $service->create($dto);

        $this->assertInstanceOf(HuntingBooking::class, $booking);
        $this->assertDatabaseHas('hunting_bookings', [
            'id' => $booking->id,
            'guide_id' => $guide->id,
            'tour_name' => 'Большая охота',
        ]);
    }

    #[Test]
    public function it_throws_guide_busy_exception_when_same_date_is_already_booked(): void
    {
        $guide = Guide::factory()->active()->create();
        $date = now()->addDays(5)->toDateString();

        // Уже есть бронь на ту же дату и гида
        HuntingBooking::factory()->create([
            'guide_id' => $guide->id,
            'date' => $date,
        ]);

        $dto = new HuntingBookingData(
            tour_name: 'Повторный тур',
            hunter_name: 'Сергей',
            guide_id: $guide->id,
            date: now()->parse($date),
            participants_count: 2,
        );

        $service = new HuntingBookingService;

        $this->expectException(GuideBusyException::class);
        $service->create($dto);
    }
}
