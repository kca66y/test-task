<?php

namespace App\DTO;

final readonly class GuideFiltersData
{
    public function __construct(
        public ?int $min_experience = null,
    ) {}

    public static function fromArray(array $input): self
    {
        return new self(
            min_experience: array_key_exists('min_experience', $input)
                ? (is_null($input['min_experience']) ? null : (int) $input['min_experience'])
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'min_experience' => $this->min_experience,
        ];
    }
}
