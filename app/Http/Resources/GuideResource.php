<?php

namespace App\Http\Resources;

use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Guide
 */
class GuideResource extends JsonResource
{
    /**
     * Преобразование модели Guide в API-формат.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'experience_years' => $this->experience_years,
            'is_active' => $this->is_active,
        ];
    }
}
