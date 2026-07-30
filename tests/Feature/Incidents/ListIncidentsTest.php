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

test('the incident catalogue eager loads kaijus in one relationship query', function () {
    $kaiju = Kaiju::factory()->create();
    Incident::factory()->count(3)->for($kaiju)->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->get(route('incidents.index'))->assertOk();

    $kaijuQueries = collect(DB::getQueryLog())
        ->filter(fn (array $query): bool => str_contains($query['query'], 'from "kaijus"'))
        ->count();

    DB::disableQueryLog();

    expect($kaijuQueries)->toBe(1);
});
