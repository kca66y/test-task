<?php

namespace App\Models;

use Database\Factories\GuideFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $experience_years
 * @property bool $is_active
 */
class Guide extends Model
{
    /** @use HasFactory<GuideFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'experience_years',
        'is_active',
    ];

    protected $casts = [
        'experience_years' => 'int',
        'is_active' => 'bool',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(HuntingBooking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
