<?php

namespace App\Models;

use App\Enums\KaijuCategory;
use Database\Factories\KaijuFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property KaijuCategory $category
 * @property int $threat_level
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'category', 'threat_level', 'description'])]
class Kaiju extends Model
{
    /** @use HasFactory<KaijuFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => KaijuCategory::class,
            'threat_level' => 'integer',
        ];
    }
}
