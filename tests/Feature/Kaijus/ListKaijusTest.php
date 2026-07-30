<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Livewire\Livewire;

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
        ->assertSee('An aerial hunter.')
        ->assertDontSee('Next');
});

test('each kaiju category uses its configured badge color', function (
    KaijuCategory $category,
    string $colorClass,
) {
    Kaiju::factory()->create(['category' => $category]);

    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee($colorClass, escape: false);
})->with([
    'aquatic' => [KaijuCategory::Aquatic, 'text-blue-800'],
    'terrestrial' => [KaijuCategory::Terrestrial, 'text-amber-700'],
    'aerial' => [KaijuCategory::Aerial, 'text-sky-800'],
    'amphibious' => [KaijuCategory::Amphibious, 'text-green-800'],
    'unknown' => [KaijuCategory::Unknown, 'text-zinc-700'],
]);

test('the catalogue displays nine kaijus per page', function () {
    foreach (range(1, 10) as $number) {
        Kaiju::factory()->create([
            'name' => sprintf('Kaiju %02d', $number),
        ]);
    }

    Livewire::test('pages::kaijus.index')
        ->assertSeeInOrder([
            'Kaiju 01',
            'Kaiju 02',
            'Kaiju 03',
            'Kaiju 04',
            'Kaiju 05',
            'Kaiju 06',
            'Kaiju 07',
            'Kaiju 08',
            'Kaiju 09',
        ])
        ->assertDontSee('Kaiju 10')
        ->assertSee('Next')
        ->call('nextPage')
        ->assertDontSee('Kaiju 09')
        ->assertSee('Kaiju 10');
});

test('the catalogue uses the configured page size', function () {
    config()->set('kers.pagination.kaijus_per_page', 2);

    foreach (range(1, 3) as $number) {
        Kaiju::factory()->create([
            'name' => sprintf('Kaiju %02d', $number),
        ]);
    }

    Livewire::test('pages::kaijus.index')
        ->assertSeeInOrder(['Kaiju 01', 'Kaiju 02'])
        ->assertDontSee('Kaiju 03')
        ->call('nextPage')
        ->assertSee('Kaiju 03');
});

test('the current catalogue page is synchronized with the url', function () {
    foreach (range(1, 10) as $number) {
        Kaiju::factory()->create([
            'name' => sprintf('Kaiju %02d', $number),
        ]);
    }

    $this->get(route('kaijus.index', ['page' => 2]))
        ->assertOk()
        ->assertDontSee('Kaiju 09')
        ->assertSee('Kaiju 10');
});

test('the catalogue can be searched by a partial case-insensitive name', function () {
    Kaiju::factory()->create(['name' => 'Abyssal Maw']);
    Kaiju::factory()->create(['name' => 'Stormwing']);

    Livewire::test('pages::kaijus.index')
        ->set('search', 'MAW')
        ->assertSee('Abyssal Maw')
        ->assertDontSee('Stormwing');
});

test('the catalogue can be filtered by category', function () {
    Kaiju::factory()->create([
        'name' => 'Stormwing',
        'category' => KaijuCategory::Aerial,
    ]);
    Kaiju::factory()->create([
        'name' => 'Abyssal Maw',
        'category' => KaijuCategory::Aquatic,
    ]);

    Livewire::test('pages::kaijus.index')
        ->set('category', KaijuCategory::Aerial->value)
        ->assertSee('Stormwing')
        ->assertDontSee('Abyssal Maw');
});

test('the catalogue can be filtered by threat level', function () {
    Kaiju::factory()->create([
        'name' => 'Stormwing',
        'threat_level' => 3,
    ]);
    Kaiju::factory()->create([
        'name' => 'Abyssal Maw',
        'threat_level' => 5,
    ]);

    Livewire::test('pages::kaijus.index')
        ->set('threatLevel', '5')
        ->assertSee('Abyssal Maw')
        ->assertDontSee('Stormwing');
});

test('search and filters combine while preserving alphabetical order', function () {
    Kaiju::factory()->create([
        'name' => 'Groundwing',
        'category' => KaijuCategory::Terrestrial,
        'threat_level' => 4,
    ]);
    Kaiju::factory()->create([
        'name' => 'Stormwing',
        'category' => KaijuCategory::Aerial,
        'threat_level' => 4,
    ]);
    Kaiju::factory()->create([
        'name' => 'Skywing',
        'category' => KaijuCategory::Aerial,
        'threat_level' => 4,
    ]);
    Kaiju::factory()->create([
        'name' => 'Cloudtalon',
        'category' => KaijuCategory::Aerial,
        'threat_level' => 3,
    ]);

    Livewire::test('pages::kaijus.index')
        ->set('search', 'wing')
        ->set('category', KaijuCategory::Aerial->value)
        ->set('threatLevel', '4')
        ->assertSeeInOrder(['Skywing', 'Stormwing'])
        ->assertDontSee('Groundwing')
        ->assertDontSee('Cloudtalon');
});

test('catalogue criteria are restored from the url', function () {
    Kaiju::factory()->create([
        'name' => 'Stormwing',
        'category' => KaijuCategory::Aerial,
        'threat_level' => 3,
    ]);
    Kaiju::factory()->create([
        'name' => 'Stormcrawler',
        'category' => KaijuCategory::Terrestrial,
        'threat_level' => 3,
    ]);
    Kaiju::factory()->create([
        'name' => 'Skybreaker',
        'category' => KaijuCategory::Aerial,
        'threat_level' => 4,
    ]);

    $this->get(route('kaijus.index', [
        'q' => 'storm',
        'category' => KaijuCategory::Aerial->value,
        'threat' => 3,
    ]))
        ->assertOk()
        ->assertSee('Stormwing')
        ->assertDontSee('Stormcrawler')
        ->assertDontSee('Skybreaker');
});

test('changing catalogue criteria resets pagination', function () {
    config()->set('kers.pagination.kaijus_per_page', 2);

    Kaiju::factory()->create([
        'name' => 'Aerial One',
        'category' => KaijuCategory::Aerial,
    ]);
    Kaiju::factory()->create([
        'name' => 'Aerial Two',
        'category' => KaijuCategory::Aerial,
    ]);
    Kaiju::factory()->create([
        'name' => 'Terrestrial One',
        'category' => KaijuCategory::Terrestrial,
    ]);
    Kaiju::factory()->create([
        'name' => 'Terrestrial Two',
        'category' => KaijuCategory::Terrestrial,
    ]);

    Livewire::test('pages::kaijus.index')
        ->call('setPage', 2)
        ->assertSee('Terrestrial One')
        ->set('category', KaijuCategory::Aerial->value)
        ->assertSeeInOrder(['Aerial One', 'Aerial Two']);
});

test('pagination retains active catalogue filters', function () {
    config()->set('kers.pagination.kaijus_per_page', 2);

    foreach (['Aerial One', 'Aerial Two', 'Aerial Three'] as $name) {
        Kaiju::factory()->create([
            'name' => $name,
            'category' => KaijuCategory::Aerial,
        ]);
    }

    Kaiju::factory()->create([
        'name' => 'Terrestrial One',
        'category' => KaijuCategory::Terrestrial,
    ]);

    Livewire::test('pages::kaijus.index')
        ->set('category', KaijuCategory::Aerial->value)
        ->assertSeeInOrder(['Aerial One', 'Aerial Three'])
        ->assertDontSee('Aerial Two')
        ->call('nextPage')
        ->assertSee('Aerial Two')
        ->assertDontSee('Terrestrial One')
        ->assertSet('category', KaijuCategory::Aerial->value);
});

test('catalogue filters can be cleared together', function () {
    Kaiju::factory()->create([
        'name' => 'Stormwing',
        'category' => KaijuCategory::Aerial,
        'threat_level' => 3,
    ]);
    Kaiju::factory()->create([
        'name' => 'Abyssal Maw',
        'category' => KaijuCategory::Aquatic,
        'threat_level' => 5,
    ]);

    Livewire::test('pages::kaijus.index')
        ->set('search', 'storm')
        ->set('category', KaijuCategory::Aerial->value)
        ->set('threatLevel', '3')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('category', '')
        ->assertSet('threatLevel', '')
        ->assertSeeInOrder(['Abyssal Maw', 'Stormwing']);
});

test('the catalogue displays a distinct empty state when criteria have no matches', function () {
    Kaiju::factory()->create(['name' => 'Stormwing']);

    Livewire::test('pages::kaijus.index')
        ->set('search', 'Leviathan')
        ->assertSee('No kaijus match the current search and filters.')
        ->assertSee('Try different criteria or clear the current filters.')
        ->assertDontSee('No kaijus have been catalogued.');
});
