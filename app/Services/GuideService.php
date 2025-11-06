<?php

namespace App\Services;

use App\DTO\GuideFiltersData;
use App\Models\Guide;
use Illuminate\Database\Eloquent\Collection;

class GuideService
{
    public function getActiveGuides(GuideFiltersData $filters): Collection
    {
        $query = Guide::active()
            ->orderByDesc('experience_years');

        if (! is_null($filters->min_experience)) {
            $query->where('experience_years', '>=', $filters->min_experience);
        }

        return $query->get();
    }
}
