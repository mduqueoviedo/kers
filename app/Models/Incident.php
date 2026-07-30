<?php

namespace App\Models;

use App\Enums\IncidentStatus;
use Carbon\CarbonImmutable;
use Database\Factories\IncidentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $location
 * @property IncidentStatus $status
 * @property CarbonImmutable $occurred_at
 * @property int $kaiju_id
 * @property string|null $source
 * @property string|null $external_event_id
 * @property string|null $external_url
 * @property float|null $magnitude
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $depth
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Kaiju $kaiju
 */
#[Fillable([
    'title',
    'description',
    'location',
    'status',
    'occurred_at',
    'kaiju_id',
    'source',
    'external_event_id',
    'external_url',
    'magnitude',
    'latitude',
    'longitude',
    'depth',
])]
class Incident extends Model
{
    /** @use HasFactory<IncidentFactory> */
    use HasFactory;

    /**
     * Get the Kaiju involved in this incident.
     *
     * @return BelongsTo<Kaiju, $this>
     */
    public function kaiju(): BelongsTo
    {
        return $this->belongsTo(Kaiju::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => IncidentStatus::class,
            'occurred_at' => 'datetime',
            'magnitude' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
            'depth' => 'float',
        ];
    }
}
