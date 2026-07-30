<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

test('guests can view the correct incident details in UTC', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Abyssal Maw']);
    $occurredAt = CarbonImmutable::parse('2026-07-29 12:30:00', 'UTC');
    $createdAt = CarbonImmutable::parse('2026-07-29 13:00:00', 'UTC');
    $updatedAt = CarbonImmutable::parse('2026-07-30 08:15:00', 'UTC');

    $incident = Incident::factory()
        ->for($kaiju)
        ->create([
            'title' => 'Atlantic shipping route breach',
            'description' => 'A deep-sea creature surfaced beneath a commercial corridor.',
            'location' => 'North Atlantic Ocean',
            'status' => IncidentStatus::Contained,
            'occurred_at' => $occurredAt,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ]);

    Incident::factory()->create(['title' => 'Unrelated incident']);

    $this->get(route('incidents.show', $incident))
        ->assertOk()
        ->assertSee('Atlantic shipping route breach')
        ->assertSee('A deep-sea creature surfaced beneath a commercial corridor.')
        ->assertSee('North Atlantic Ocean')
        ->assertSee('Contained')
        ->assertSee('Abyssal Maw')
        ->assertSee('July 29, 2026 12:30 PM UTC')
        ->assertSee('July 29, 2026 1:00 PM UTC')
        ->assertSee('July 30, 2026 8:15 AM UTC')
        ->assertSee($occurredAt->toIso8601String())
        ->assertSee($createdAt->toIso8601String())
        ->assertSee($updatedAt->toIso8601String())
        ->assertSee('text-amber-700', escape: false)
        ->assertSee(route('kaijus.show', $kaiju), escape: false)
        ->assertSee(route('incidents.index'), escape: false)
        ->assertSee('aria-label="Breadcrumb"', escape: false)
        ->assertSeeInOrder(['Kaijus', 'Abyssal Maw', 'Incident details'])
        ->assertDontSee('Back to incident catalogue')
        ->assertDontSee('Unrelated incident');
});

test('the detail component receives its route-bound incident', function () {
    $incident = Incident::factory()->create([
        'title' => 'Route-bound incident',
    ]);

    Livewire::test('pages::incidents.show', ['incident' => $incident])
        ->assertSet('incident.id', $incident->id)
        ->assertSee('Route-bound incident')
        ->assertSee($incident->kaiju->name);
});

test('an unknown incident returns not found', function () {
    $this->get(route('incidents.show', ['incident' => 999_999]))
        ->assertNotFound();
});

test('incident catalogue cards link to their own detail pages', function () {
    $firstIncident = Incident::factory()->create([
        'title' => 'First catalogue incident',
    ]);
    $secondIncident = Incident::factory()->create([
        'title' => 'Second catalogue incident',
    ]);

    $this->get(route('incidents.index'))
        ->assertOk()
        ->assertSee(route('incidents.show', $firstIncident), escape: false)
        ->assertSee(route('incidents.show', $secondIncident), escape: false);
});
