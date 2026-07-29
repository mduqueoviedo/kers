<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;

test('guests can view the kaiju catalogue', function () {
    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee('Kaiju catalogue')
        ->assertSee('Kaijus')
        ->assertDontSee('Dashboard')
        ->assertDontSee('Settings')
        ->assertDontSee('Repository')
        ->assertDontSee('Documentation')
        ->assertDontSee('Log out');
});

test('the catalogue displays an empty state', function () {
    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee('No kaijus have been catalogued.')
        ->assertSee('Known creatures will appear here once they are registered.');
});

test('the catalogue displays existing kaijus ordered by name', function () {
    Kaiju::factory()->create([
        'name' => 'Stormwing',
        'category' => KaijuCategory::Aerial,
        'threat_level' => 3,
        'description' => 'An aerial hunter.',
    ]);

    Kaiju::factory()->create([
        'name' => 'Abyssal Maw',
        'category' => KaijuCategory::Aquatic,
        'threat_level' => 5,
        'description' => null,
    ]);

    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSeeInOrder(['Abyssal Maw', 'Stormwing'])
        ->assertSee('Aquatic')
        ->assertSee('Aerial')
        ->assertSee('Threat level 5 of 5')
        ->assertSee('Threat level 3 of 5')
        ->assertSee('No description provided.')
        ->assertSee('An aerial hunter.');
});
