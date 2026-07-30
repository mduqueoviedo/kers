<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('guests can view the incident catalogue and its navigation', function () {
    $this->get(route('incidents.index'))
        ->assertOk()
        ->assertSee('Incident catalogue')
        ->assertSee('Kaijus')
        ->assertSee('Incidents')
        ->assertSee('Record incident')
        ->assertSee('Search by title or location')
        ->assertSee('All statuses')
        ->assertSee('All Kaijus')
        ->assertSee('Newest first')
        ->assertSee('Oldest first')
        ->assertSee(route('incidents.create'), escape: false);
});

test('the incident catalogue displays an empty state', function () {
    $this->get(route('incidents.index'))
        ->assertOk()
        ->assertSee('No incidents have been recorded.')
        ->assertSee('Recorded Kaiju activity will appear here.');
});

test('incidents are displayed by most recent occurrence with their kaiju', function () {
    $olderKaiju = Kaiju::factory()->create(['name' => 'Abyssal Maw']);
    $newerKaiju = Kaiju::factory()->create(['name' => 'Stormwing']);

    Incident::factory()
        ->for($olderKaiju)
        ->closed()
        ->create([
            'title' => 'Harbour disturbance',
            'location' => 'Port Helios',
            'occurred_at' => CarbonImmutable::parse('2026-07-28 08:15', 'UTC'),
        ]);

    Incident::factory()
        ->for($newerKaiju)
        ->open()
        ->create([
            'title' => 'Northern airspace breach',
            'location' => 'Sector Seven',
            'occurred_at' => CarbonImmutable::parse('2026-07-29 12:30', 'UTC'),
        ]);

    $this->get(route('incidents.index'))
        ->assertOk()
        ->assertSeeInOrder(['Northern airspace breach', 'Harbour disturbance'])
        ->assertSee('Sector Seven')
        ->assertSee('Port Helios')
        ->assertSee('Open')
        ->assertSee('Closed')
        ->assertSee('Stormwing')
        ->assertSee('Abyssal Maw')
        ->assertSee('Occurred Jul 29, 2026, 12:30 UTC')
        ->assertSee('Occurred Jul 28, 2026, 08:15 UTC')
        ->assertSee(route('kaijus.show', $newerKaiju), escape: false)
        ->assertSee('Incident record')
        ->assertSee('border-t-orange-500', escape: false)
        ->assertDontSee('Next');
});

test('each incident status uses its configured badge color', function (
    IncidentStatus $status,
    string $colorClass,
) {
    Incident::factory()->create(['status' => $status]);

    $this->get(route('incidents.index'))
        ->assertOk()
        ->assertSee($colorClass, escape: false);
})->with([
    'open' => [IncidentStatus::Open, 'text-red-700'],
    'contained' => [IncidentStatus::Contained, 'text-amber-700'],
    'closed' => [IncidentStatus::Closed, 'text-green-800'],
]);

test('the incident catalogue displays nine incidents per page', function () {
    $kaiju = Kaiju::factory()->create();
    $startingTime = CarbonImmutable::parse('2026-07-29 12:30', 'UTC');

    foreach (range(1, 10) as $number) {
        Incident::factory()
            ->for($kaiju)
            ->create([
                'title' => sprintf('Incident %02d', $number),
                'occurred_at' => $startingTime->subMinutes($number),
            ]);
    }

    Livewire::test('pages::incidents.index')
        ->assertSeeInOrder([
            'Incident 01',
            'Incident 02',
            'Incident 03',
            'Incident 04',
            'Incident 05',
            'Incident 06',
            'Incident 07',
            'Incident 08',
            'Incident 09',
        ])
        ->assertDontSee('Incident 10')
        ->assertSee('Next')
        ->call('nextPage')
        ->assertDontSee('Incident 09')
        ->assertSee('Incident 10');
});

test('the incident catalogue uses the configured page size', function () {
    config()->set('kers.pagination.incidents_per_page', 2);

    $kaiju = Kaiju::factory()->create();
    $startingTime = CarbonImmutable::parse('2026-07-29 12:30', 'UTC');

    foreach (range(1, 3) as $number) {
        Incident::factory()
            ->for($kaiju)
            ->create([
                'title' => sprintf('Incident %02d', $number),
                'occurred_at' => $startingTime->subMinutes($number),
            ]);
    }

    Livewire::test('pages::incidents.index')
        ->assertSeeInOrder(['Incident 01', 'Incident 02'])
        ->assertDontSee('Incident 03')
        ->call('nextPage')
        ->assertSee('Incident 03');
});

test('the current incident catalogue page is synchronized with the url', function () {
    $kaiju = Kaiju::factory()->create();
    $startingTime = CarbonImmutable::parse('2026-07-29 12:30', 'UTC');

    foreach (range(1, 10) as $number) {
        Incident::factory()
            ->for($kaiju)
            ->create([
                'title' => sprintf('Incident %02d', $number),
                'occurred_at' => $startingTime->subMinutes($number),
            ]);
    }

    $this->get(route('incidents.index', ['page' => 2]))
        ->assertOk()
        ->assertDontSee('Incident 09')
        ->assertSee('Incident 10');
});

test('the incident catalogue can be searched by title or location case-insensitively', function () {
    $kaiju = Kaiju::factory()->create();

    Incident::factory()->for($kaiju)->create([
        'title' => 'Harbour evacuation',
        'location' => 'Western coast',
    ]);
    Incident::factory()->for($kaiju)->create([
        'title' => 'Transit interruption',
        'location' => 'Harbour district',
    ]);
    Incident::factory()->for($kaiju)->create([
        'title' => 'Mountain alert',
        'location' => 'Northern ridge',
    ]);

    Livewire::test('pages::incidents.index')
        ->set('search', 'HARBOUR')
        ->assertSee('Harbour evacuation')
        ->assertSee('Transit interruption')
        ->assertDontSee('Mountain alert');
});

test('the incident catalogue can be filtered by status', function () {
    $kaiju = Kaiju::factory()->create();

    Incident::factory()->for($kaiju)->open()->create(['title' => 'Open incident']);
    Incident::factory()->for($kaiju)->closed()->create(['title' => 'Closed incident']);

    Livewire::test('pages::incidents.index')
        ->set('status', IncidentStatus::Closed->value)
        ->assertSee('Closed incident')
        ->assertDontSee('Open incident');
});

test('the incident catalogue can be filtered by kaiju', function () {
    $stormwing = Kaiju::factory()->create(['name' => 'Stormwing']);
    $abyssalMaw = Kaiju::factory()->create(['name' => 'Abyssal Maw']);

    Incident::factory()->for($stormwing)->create(['title' => 'Airspace incident']);
    Incident::factory()->for($abyssalMaw)->create(['title' => 'Harbour incident']);

    Livewire::test('pages::incidents.index')
        ->set('kaijuId', (string) $stormwing->id)
        ->assertSee('Airspace incident')
        ->assertDontSee('Harbour incident');
});

test('incidents can be ordered by oldest occurrence first', function () {
    $kaiju = Kaiju::factory()->create();

    Incident::factory()->for($kaiju)->create([
        'title' => 'Older incident',
        'occurred_at' => CarbonImmutable::parse('2026-07-28 08:15', 'UTC'),
    ]);
    Incident::factory()->for($kaiju)->create([
        'title' => 'Newer incident',
        'occurred_at' => CarbonImmutable::parse('2026-07-29 12:30', 'UTC'),
    ]);

    Livewire::test('pages::incidents.index')
        ->set('sort', 'oldest')
        ->assertSeeInOrder(['Older incident', 'Newer incident']);
});

test('incident catalogue criteria combine with the selected occurrence order', function () {
    $selectedKaiju = Kaiju::factory()->create();
    $otherKaiju = Kaiju::factory()->create();

    Incident::factory()->for($selectedKaiju)->open()->create([
        'title' => 'Harbour Alpha',
        'occurred_at' => CarbonImmutable::parse('2026-07-28 08:15', 'UTC'),
    ]);
    Incident::factory()->for($selectedKaiju)->open()->create([
        'title' => 'Harbour Beta',
        'occurred_at' => CarbonImmutable::parse('2026-07-29 12:30', 'UTC'),
    ]);
    Incident::factory()->for($selectedKaiju)->closed()->create(['title' => 'Harbour Closed']);
    Incident::factory()->for($otherKaiju)->open()->create(['title' => 'Harbour Other']);

    Livewire::test('pages::incidents.index')
        ->set('search', 'harbour')
        ->set('status', IncidentStatus::Open->value)
        ->set('kaijuId', (string) $selectedKaiju->id)
        ->set('sort', 'oldest')
        ->assertSeeInOrder(['Harbour Alpha', 'Harbour Beta'])
        ->assertDontSee('Harbour Closed')
        ->assertDontSee('Harbour Other');
});

test('incident catalogue criteria are restored from the url', function () {
    $selectedKaiju = Kaiju::factory()->create();
    $otherKaiju = Kaiju::factory()->create();

    Incident::factory()->for($selectedKaiju)->open()->create([
        'title' => 'Harbour Older',
        'occurred_at' => CarbonImmutable::parse('2026-07-28 08:15', 'UTC'),
    ]);
    Incident::factory()->for($selectedKaiju)->open()->create([
        'title' => 'Harbour Newer',
        'occurred_at' => CarbonImmutable::parse('2026-07-29 12:30', 'UTC'),
    ]);
    Incident::factory()->for($otherKaiju)->open()->create(['title' => 'Harbour Other']);

    $this->get(route('incidents.index', [
        'q' => 'harbour',
        'status' => IncidentStatus::Open->value,
        'kaiju' => $selectedKaiju->id,
        'sort' => 'oldest',
    ]))
        ->assertOk()
        ->assertSeeInOrder(['Harbour Older', 'Harbour Newer'])
        ->assertDontSee('Harbour Other');
});

test('changing incident catalogue criteria resets pagination', function () {
    config()->set('kers.pagination.incidents_per_page', 2);

    $kaiju = Kaiju::factory()->create();
    $startingTime = CarbonImmutable::parse('2026-07-29 12:30', 'UTC');

    foreach (range(1, 2) as $number) {
        Incident::factory()->for($kaiju)->closed()->create([
            'title' => sprintf('Closed %02d', $number),
            'occurred_at' => $startingTime->subMinutes($number),
        ]);
        Incident::factory()->for($kaiju)->open()->create([
            'title' => sprintf('Open %02d', $number),
            'occurred_at' => $startingTime->subDays(2)->subMinutes($number),
        ]);
    }

    Livewire::test('pages::incidents.index')
        ->call('setPage', 2)
        ->assertSee('Open 01')
        ->set('status', IncidentStatus::Open->value)
        ->assertSeeInOrder(['Open 01', 'Open 02']);
});

test('pagination retains active incident catalogue filters', function () {
    config()->set('kers.pagination.incidents_per_page', 2);

    $kaiju = Kaiju::factory()->create();

    foreach (range(1, 3) as $number) {
        Incident::factory()->for($kaiju)->open()->create([
            'title' => sprintf('Open %02d', $number),
        ]);
    }

    Incident::factory()->for($kaiju)->closed()->create(['title' => 'Closed incident']);

    Livewire::test('pages::incidents.index')
        ->set('status', IncidentStatus::Open->value)
        ->assertDontSee('Closed incident')
        ->call('nextPage')
        ->assertSee('Open')
        ->assertDontSee('Closed incident')
        ->assertSet('status', IncidentStatus::Open->value);
});

test('incident catalogue filters can be cleared together', function () {
    $kaiju = Kaiju::factory()->create();
    Incident::factory()->for($kaiju)->create(['title' => 'Harbour incident']);
    Incident::factory()->create(['title' => 'Mountain incident']);

    Livewire::test('pages::incidents.index')
        ->set('search', 'harbour')
        ->set('status', IncidentStatus::Open->value)
        ->set('kaijuId', (string) $kaiju->id)
        ->set('sort', 'oldest')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('status', '')
        ->assertSet('kaijuId', '')
        ->assertSet('sort', 'newest')
        ->assertSee('Harbour incident')
        ->assertSee('Mountain incident');
});

test('the incident catalogue displays a distinct empty state when criteria have no matches', function () {
    Incident::factory()->create(['title' => 'Harbour incident']);

    Livewire::test('pages::incidents.index')
        ->set('search', 'mountain')
        ->assertSee('No incidents match the current search and filters.')
        ->assertSee('Try different criteria or clear the current filters.')
        ->assertDontSee('No incidents have been recorded.');
});

test('the incident catalogue eager loads kaijus in one relationship query', function () {
    $kaiju = Kaiju::factory()->create();
    Incident::factory()->count(3)->for($kaiju)->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->get(route('incidents.index'))->assertOk();

    $kaijuRelationshipQueries = collect(DB::getQueryLog())
        ->filter(
            fn (array $query): bool => str_contains(
                $query['query'],
                'from "kaijus" where "kaijus"."id" in',
            ),
        )
        ->count();

    DB::disableQueryLog();

    expect($kaijuRelationshipQueries)->toBe(1);
});
