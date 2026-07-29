<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Database\Seeders\DatabaseSeeder;

test('the database seeder creates a repeatable representative catalogue', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Kaiju::query()->count())->toBe(12);

    $expectedKaijus = [
        ['Abyssal Maw', KaijuCategory::Aquatic, 5],
        ['Brinehide', KaijuCategory::Aquatic, 2],
        ['Cinderhorn', KaijuCategory::Terrestrial, 4],
        ['Cloudtalon', KaijuCategory::Aerial, 3],
        ['Emberfin', KaijuCategory::Amphibious, 2],
        ['Frostveil', KaijuCategory::Unknown, 3],
        ['Graniteback', KaijuCategory::Terrestrial, 4],
        ['Ironjaw', KaijuCategory::Terrestrial, 5],
        ['Skybreaker', KaijuCategory::Aerial, 3],
        ['Mireclaw', KaijuCategory::Amphibious, 2],
        ['Tidecaller', KaijuCategory::Amphibious, 4],
        ['Unknown Titan', KaijuCategory::Unknown, 1],
    ];

    foreach ($expectedKaijus as [$name, $category, $threatLevel]) {
        $this->assertDatabaseHas('kaijus', [
            'name' => $name,
            'category' => $category->value,
            'threat_level' => $threatLevel,
        ]);
    }

    $this->assertDatabaseHas('kaijus', [
        'name' => 'Frostveil',
        'description' => null,
    ]);
});
