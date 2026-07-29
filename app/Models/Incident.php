<?php

namespace App\Models;

use App\Enums\IncidentStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Kaiju $kaiju
 */
#[Fillable(['title', 'description', 'location', 'status', 'occurred_at', 'kaiju_id'])]
class Incident extends Model
{
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
        ];
    }
}
