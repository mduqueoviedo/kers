<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

test('a kaiju can be persisted with its category cast to an enum', function () {
    $kaiju = Kaiju::query()->create([
        'name' => 'Leviathan',
        'category' => KaijuCategory::Aquatic,
        'threat_level' => 4,
        'description' => null,
    ])->refresh();

    expect($kaiju->name)->toBe('Leviathan')
        ->and($kaiju->category)->toBe(KaijuCategory::Aquatic)
        ->and($kaiju->threat_level)->toBe(4)
        ->and($kaiju->description)->toBeNull();

    $this->assertDatabaseHas('kaijus', [
        'name' => 'Leviathan',
        'category' => KaijuCategory::Aquatic->value,
        'threat_level' => 4,
        'description' => null,
    ]);
});

test('the database rejects an invalid kaiju category', function () {
    expect(fn () => DB::table('kaijus')->insert([
        'name' => 'Unclassified',
        'category' => 'cosmic',
        'threat_level' => 3,
    ]))->toThrow(QueryException::class);
});

test('the database accepts every kaiju category', function (KaijuCategory $category) {
    $kaiju = Kaiju::query()->create([
        'name' => 'Catalogued specimen',
        'category' => $category,
        'threat_level' => 3,
    ])->refresh();

    expect($kaiju->category)->toBe($category);
})->with(KaijuCategory::cases());

test('the database accepts the threat-level boundaries', function (int $threatLevel) {
    $kaiju = Kaiju::query()->create([
        'name' => 'Rated specimen',
        'category' => KaijuCategory::Unknown,
        'threat_level' => $threatLevel,
    ])->refresh();

    expect($kaiju->threat_level)->toBe($threatLevel);
})->with([
    'minimum' => 1,
    'maximum' => 5,
]);

test('the database rejects a threat level outside the allowed range', function (int $threatLevel) {
    expect(fn () => DB::table('kaijus')->insert([
        'name' => 'Unrated',
        'category' => KaijuCategory::Unknown->value,
        'threat_level' => $threatLevel,
    ]))->toThrow(QueryException::class);
})->with([
    'below the minimum' => 0,
    'above the maximum' => 6,
]);
