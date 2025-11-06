<?php

namespace App\DTO;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Data Transfer Object для бронирования охотничьего тура.
 */
readonly class HuntingBookingData implements Arrayable
{
    public function __construct(
        public string $tour_name,
        public string $hunter_name,
        public int $guide_id,
        public Carbon $date,
        public int $participants_count,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tour_name: $data['tour_name'],
            hunter_name: $data['hunter_name'],
            guide_id: (int) $data['guide_id'],
            date: Carbon::parse($data['date']),
            participants_count: (int) $data['participants_count'],
        );
    }

    public function toArray(): array
    {
        return [
            'tour_name' => $this->tour_name,
            'hunter_name' => $this->hunter_name,
            'guide_id' => $this->guide_id,
            'date' => $this->date->toDateString(),
            'participants_count' => $this->participants_count,
        ];
    }
}
