<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use App\Models\User;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('authenticated users can view the incident form with ordered kaijus', function () {
    Kaiju::factory()->create(['name' => 'Stormwing']);
    Kaiju::factory()->create(['name' => 'Abyssal Maw']);

    $this->get(route('incidents.create'))
        ->assertOk()
        ->assertSee('Record incident')
        ->assertSee('Occurred at (UTC)')
        ->assertSeeInOrder(['Abyssal Maw', 'Stormwing']);
});

test('a valid incident can be recorded for a known kaiju in UTC', function () {
    $kaiju = Kaiju::factory()->create();

    Livewire::test('pages::incidents.create')
        ->set('title', 'Atlantic coastal breach')
        ->set('description', 'A creature surfaced near a commercial shipping route.')
        ->set('location', 'North Atlantic Ocean')
        ->set('status', IncidentStatus::Open->value)
        ->set('occurred_at', '2026-07-29T12:30')
        ->set('kaiju_id', (string) $kaiju->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('toast-show')
        ->assertRedirect(route('kaijus.show', $kaiju));

    $incident = Incident::query()->sole();

    expect($incident->title)->toBe('Atlantic coastal breach')
        ->and($incident->description)->toBe('A creature surfaced near a commercial shipping route.')
        ->and($incident->location)->toBe('North Atlantic Ocean')
        ->and($incident->status)->toBe(IncidentStatus::Open)
        ->and($incident->occurred_at)->toBeInstanceOf(CarbonImmutable::class)
        ->and($incident->occurred_at->timezoneName)->toBe('UTC')
        ->and($incident->occurred_at->toDateTimeString())->toBe('2026-07-29 12:30:00')
        ->and($incident->kaiju->is($kaiju))->toBeTrue();
});

test('the incident form defaults to open status', function () {
    Kaiju::factory()->create();

    Livewire::test('pages::incidents.create')
        ->assertSet('status', IncidentStatus::Open->value);
});

test('a kaiju can be preselected from the url', function () {
    $kaiju = Kaiju::factory()->create();

    Livewire::withQueryParams(['kaiju' => $kaiju->id])
        ->test('pages::incidents.create')
        ->assertSet('kaiju_id', (string) $kaiju->id);
});

test('required incident fields are validated', function () {
    Kaiju::factory()->create();

    Livewire::test('pages::incidents.create')
        ->set('status', '')
        ->call('save')
        ->assertHasErrors([
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'status' => 'required',
            'occurred_at' => 'required',
            'kaiju_id' => 'required',
        ]);

    $this->assertDatabaseCount('incidents', 0);
});

test('incident text fields respect their database column lengths', function (string $field) {
    $kaiju = Kaiju::factory()->create();

    Livewire::test('pages::incidents.create')
        ->set('title', 'Valid title')
        ->set('description', 'Valid description')
        ->set('location', 'Valid location')
        ->set('occurred_at', '2026-07-29T12:30')
        ->set('kaiju_id', (string) $kaiju->id)
        ->set($field, str_repeat('X', 256))
        ->call('save')
        ->assertHasErrors([$field => 'max']);

    $this->assertDatabaseCount('incidents', 0);
})->with(['title', 'location']);

test('the incident status must be supported', function () {
    $kaiju = Kaiju::factory()->create();

    Livewire::test('pages::incidents.create')
        ->set('title', 'Invalid status incident')
        ->set('description', 'This incident must not be stored.')
        ->set('location', 'Test sector')
        ->set('status', 'monitoring')
        ->set('occurred_at', '2026-07-29T12:30')
        ->set('kaiju_id', (string) $kaiju->id)
        ->call('save')
        ->assertHasErrors(['status']);

    $this->assertDatabaseCount('incidents', 0);
});

test('the incident occurrence time must use the expected UTC input format', function () {
    $kaiju = Kaiju::factory()->create();

    Livewire::test('pages::incidents.create')
        ->set('title', 'Invalid date incident')
        ->set('description', 'This incident must not be stored.')
        ->set('location', 'Test sector')
        ->set('occurred_at', 'July 29 at noon')
        ->set('kaiju_id', (string) $kaiju->id)
        ->call('save')
        ->assertHasErrors(['occurred_at' => 'date_format']);

    $this->assertDatabaseCount('incidents', 0);
});

test('an invalid preselected kaiju cannot be persisted', function (
    string $kaijuId,
    string $validationRule,
) {
    Livewire::withQueryParams(['kaiju' => $kaijuId])
        ->test('pages::incidents.create')
        ->set('title', 'Unlinked incident')
        ->set('description', 'This incident must not be stored.')
        ->set('location', 'Test sector')
        ->set('occurred_at', '2026-07-29T12:30')
        ->call('save')
        ->assertHasErrors(['kaiju_id' => $validationRule]);

    $this->assertDatabaseCount('incidents', 0);
})->with([
    'unknown identifier' => ['999999', 'exists'],
    'non-numeric identifier' => ['not-a-kaiju', 'integer'],
]);

test('the incident form explains when no kaiju is available', function () {
    $this->get(route('incidents.create'))
        ->assertOk()
        ->assertSee('A known Kaiju is required before recording an incident.')
        ->assertSee('Register kaiju')
        ->assertDontSee('Occurred at (UTC)');
});

test('the catalogue and kaiju details link to the incident form', function () {
    $kaiju = Kaiju::factory()->create();

    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee(route('incidents.create'), escape: false);

    $this->get(route('kaijus.show', $kaiju))
        ->assertOk()
        ->assertSee(
            route('incidents.create', ['kaiju' => $kaiju->id]),
            escape: false,
        );
});
