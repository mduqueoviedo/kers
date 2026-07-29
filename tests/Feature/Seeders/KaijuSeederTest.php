<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Database\Seeders\DatabaseSeeder;

test('the database seeder creates a repeatable representative catalogue', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Kaiju::query()->count())->toBe(5);

    $expectedKaijus = [
        ['Abyssal Maw', KaijuCategory::Aquatic, 5],
        ['Graniteback', KaijuCategory::Terrestrial, 4],
        ['Skybreaker', KaijuCategory::Aerial, 3],
        ['Mireclaw', KaijuCategory::Amphibious, 2],
        ['Unknown Titan', KaijuCategory::Unknown, 1],
    ];

    foreach ($expectedKaijus as [$name, $category, $threatLevel]) {
        $this->assertDatabaseHas('kaijus', [
            'name' => $name,
            'category' => $category->value,
            'threat_level' => $threatLevel,
        ]);
    }
});
