<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

test('guests can view a prefilled incident edit form with ordered kaijus', function () {
    $currentKaiju = Kaiju::factory()->create(['name' => 'Stormwing']);
    Kaiju::factory()->create(['name' => 'Abyssal Maw']);
    $incident = Incident::factory()->for($currentKaiju)->create([
        'title' => 'Airport perimeter breach',
        'description' => 'An aerial creature crossed the restricted perimeter.',
        'location' => 'Northern airfield',
        'status' => IncidentStatus::Contained,
        'occurred_at' => CarbonImmutable::parse('2026-07-29 12:30:00', 'UTC'),
    ]);

    $this->get(route('incidents.edit', $incident))
        ->assertOk()
        ->assertSee('Edit incident')
        ->assertSeeInOrder(['Abyssal Maw', 'Stormwing']);

    Livewire::test('pages::incidents.edit', ['incident' => $incident])
        ->assertSet('incident.id', $incident->id)
        ->assertSet('title', 'Airport perimeter breach')
        ->assertSet('description', 'An aerial creature crossed the restricted perimeter.')
        ->assertSet('location', 'Northern airfield')
        ->assertSet('status', IncidentStatus::Contained->value)
        ->assertSet('occurred_at', '2026-07-29T12:30')
        ->assertSet('kaiju_id', (string) $currentKaiju->id);
});

test('a valid incident edit is persisted in UTC', function () {
    $originalKaiju = Kaiju::factory()->create();
    $newKaiju = Kaiju::factory()->create();
    $createdAt = CarbonImmutable::parse('2026-01-15 14:30:00', 'UTC');
    $updatedAt = CarbonImmutable::parse('2026-07-30 09:45:00', 'UTC');
    $incident = Incident::factory()->for($originalKaiju)->create([
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);

    $this->travelTo($updatedAt);

    Livewire::test('pages::incidents.edit', ['incident' => $incident])
        ->set('title', 'Updated coastal evacuation')
        ->set('description', 'The evacuation zone now extends across the eastern coast.')
        ->set('location', 'Eastern coastline')
        ->set('status', IncidentStatus::Closed->value)
        ->set('occurred_at', '2026-07-29T18:45')
        ->set('kaiju_id', (string) $newKaiju->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('toast-show')
        ->assertRedirect(route('incidents.show', $incident));

    $incident->refresh();

    expect($incident)
        ->title->toBe('Updated coastal evacuation')
        ->description->toBe('The evacuation zone now extends across the eastern coast.')
        ->location->toBe('Eastern coastline')
        ->status->toBe(IncidentStatus::Closed)
        ->kaiju_id->toBe($newKaiju->id)
        ->and($incident->occurred_at)->toBeInstanceOf(CarbonImmutable::class)
        ->and($incident->occurred_at->timezoneName)->toBe('UTC')
        ->and($incident->occurred_at->toDateTimeString())->toBe('2026-07-29 18:45:00')
        ->and($incident->created_at?->equalTo($createdAt))->toBeTrue()
        ->and($incident->updated_at?->equalTo($updatedAt))->toBeTrue();
});

test('an incident can be changed to each supported status', function (IncidentStatus $status) {
    $incident = Incident::factory()->create();

    Livewire::test('pages::incidents.edit', ['incident' => $incident])
        ->set('status', $status->value)
        ->call('save')
        ->assertHasNoErrors();

    expect($incident->fresh()?->status)->toBe($status);
})->with(IncidentStatus::cases());

test('required fields are validated before updating an incident', function () {
    $incident = Incident::factory()->create(['title' => 'Original incident']);

    Livewire::test('pages::incidents.edit', ['incident' => $incident])
        ->set('title', '')
        ->set('description', '')
        ->set('location', '')
        ->set('status', '')
        ->set('occurred_at', '')
        ->set('kaiju_id', '')
        ->call('save')
        ->assertHasErrors([
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'status' => 'required',
            'occurred_at' => 'required',
            'kaiju_id' => 'required',
        ]);

    expect($incident->fresh()?->title)->toBe('Original incident');
});

test('invalid incident fields do not update the record', function (
    string $field,
    string $value,
    ?string $rule,
) {
    $incident = Incident::factory()->create(['title' => 'Original incident']);

    $component = Livewire::test('pages::incidents.edit', ['incident' => $incident])
        ->set($field, $value)
        ->call('save');

    $rule === null
        ? $component->assertHasErrors([$field])
        : $component->assertHasErrors([$field => $rule]);

    expect($incident->fresh()?->title)->toBe('Original incident');
})->with([
    'title above database length' => ['title', str_repeat('T', 256), 'max'],
    'location above database length' => ['location', str_repeat('L', 256), 'max'],
    'unsupported status' => ['status', 'monitoring', null],
    'invalid occurrence format' => ['occurred_at', 'July 29 at noon', 'date_format'],
    'unknown kaiju' => ['kaiju_id', '999999', 'exists'],
    'non-numeric kaiju' => ['kaiju_id', 'not-a-kaiju', 'integer'],
]);

test('an unknown incident edit page returns not found', function () {
    $this->get(route('incidents.edit', ['incident' => 999_999]))
        ->assertNotFound();
});

test('incident details link to editing and the form can return to details', function () {
    $incident = Incident::factory()->create();

    $this->get(route('incidents.show', $incident))
        ->assertOk()
        ->assertSee(route('incidents.edit', $incident), escape: false);

    $this->get(route('incidents.edit', $incident))
        ->assertOk()
        ->assertSee(route('incidents.show', $incident), escape: false);
});
