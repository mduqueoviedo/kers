<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;

test('the kaiju factory creates a valid persisted model', function () {
    $kaiju = Kaiju::factory()->create();

    expect($kaiju->name)->not->toBeEmpty()
        ->and($kaiju->category)->toBeInstanceOf(KaijuCategory::class)
        ->and($kaiju->threat_level)->toBeGreaterThanOrEqual(1)
        ->and($kaiju->threat_level)->toBeLessThanOrEqual(5)
        ->and($kaiju->description)->toBeString();

    $this->assertDatabaseHas('kaijus', [
        'id' => $kaiju->id,
        'category' => $kaiju->category->value,
        'threat_level' => $kaiju->threat_level,
    ]);
});

test('the kaiju factory can omit the optional description', function () {
    $kaiju = Kaiju::factory()->withoutDescription()->create();

    expect($kaiju->description)->toBeNull();
});
