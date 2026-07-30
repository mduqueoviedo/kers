<?php

use App\Enums\KaijuCategory;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('guests can view the correct kaiju details', function () {
    $createdAt = Carbon::parse('2026-01-15 14:30:00');
    $updatedAt = Carbon::parse('2026-02-20 09:45:00');

    $kaiju = Kaiju::factory()->create([
        'name' => 'Leviathan',
        'category' => KaijuCategory::Aquatic,
        'threat_level' => 5,
        'description' => 'A colossal creature detected beneath the Atlantic.',
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ]);

    Kaiju::factory()->create([
        'name' => 'Stormwing',
    ]);

    $this->get(route('kaijus.show', $kaiju))
        ->assertOk()
        ->assertSee('Leviathan')
        ->assertSee('Aquatic')
        ->assertSee('Level 5 of 5')
        ->assertSee('A colossal creature detected beneath the Atlantic.')
        ->assertSee($createdAt->isoFormat('LLL'))
        ->assertSee($updatedAt->isoFormat('LLL'))
        ->assertSee($createdAt->toIso8601String())
        ->assertSee($updatedAt->toIso8601String())
        ->assertDontSee($createdAt->toDateTimeString())
        ->assertDontSee($updatedAt->toDateTimeString())
        ->assertDontSee('Stormwing');
});

test('the detail component displays the optional description fallback', function () {
    $kaiju = Kaiju::factory()->withoutDescription()->create([
        'name' => 'Frostveil',
    ]);

    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->assertSet('kaiju.id', $kaiju->id)
        ->assertSee('Frostveil')
        ->assertSee('No description provided.');
});

test('an unknown kaiju returns not found', function () {
    $this->get(route('kaijus.show', ['kaiju' => 999_999]))
        ->assertNotFound();
});

test('catalogue cards link to their own detail pages', function () {
    $firstKaiju = Kaiju::factory()->create(['name' => 'Abyssal Maw']);
    $secondKaiju = Kaiju::factory()->create(['name' => 'Stormwing']);

    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee(route('kaijus.show', $firstKaiju), escape: false)
        ->assertSee(route('kaijus.show', $secondKaiju), escape: false);
});

test('a kaiju detail page shows its incident history newest first', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Leviathan']);
    $otherKaiju = Kaiju::factory()->create();

    $olderIncident = Incident::factory()->for($kaiju)->open()->create([
        'title' => 'Atlantic shelf disturbance',
        'location' => 'North Atlantic',
        'occurred_at' => CarbonImmutable::parse('2026-07-28 08:15', 'UTC'),
    ]);
    $newerIncident = Incident::factory()->for($kaiju)->contained()->create([
        'title' => 'Reykjavik coastal alert',
        'location' => 'Reykjavik, Iceland',
        'occurred_at' => CarbonImmutable::parse('2026-07-29 12:30', 'UTC'),
    ]);
    Incident::factory()->for($otherKaiju)->closed()->create([
        'title' => 'Unrelated incident',
    ]);

    $this->get(route('kaijus.show', $kaiju))
        ->assertOk()
        ->assertSee('Incident history')
        ->assertSeeInOrder([
            'Reykjavik coastal alert',
            'Atlantic shelf disturbance',
        ])
        ->assertSee('Contained')
        ->assertSee('Open')
        ->assertSee('Reykjavik, Iceland')
        ->assertSee('North Atlantic')
        ->assertSee('Occurred July 29, 2026 12:30 PM UTC')
        ->assertSee('Occurred July 28, 2026 8:15 AM UTC')
        ->assertSee(route('incidents.show', $newerIncident), escape: false)
        ->assertSee(route('incidents.show', $olderIncident), escape: false)
        ->assertSee('Incident record')
        ->assertSee('border-t-orange-500', escape: false)
        ->assertDontSee('Unrelated incident');
});

test('a kaiju detail page explains when its incident history is empty', function () {
    $kaiju = Kaiju::factory()->create();

    $this->get(route('kaijus.show', $kaiju))
        ->assertOk()
        ->assertSee('No incidents have been recorded for this Kaiju.')
        ->assertSee('New activity involving this creature will appear here.');
});

test('a kaiju detail page eager loads its incident history in one relationship query', function () {
    $kaiju = Kaiju::factory()->create();
    Incident::factory()->count(3)->for($kaiju)->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->get(route('kaijus.show', $kaiju))->assertOk();

    $incidentRelationshipQueries = collect(DB::getQueryLog())
        ->filter(
            fn (array $query): bool => str_contains(
                $query['query'],
                'from "incidents" where "incidents"."kaiju_id" in',
            ),
        )
        ->count();

    DB::disableQueryLog();

    expect($incidentRelationshipQueries)->toBe(1);
});
