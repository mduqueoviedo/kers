<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function usgsImportResponse(string $title = 'M 5.6 - 10 km south of Example'): array
{
    return [
        'type' => 'FeatureCollection',
        'features' => [[
            'id' => 'us7000example',
            'properties' => [
                'mag' => 5.6,
                'place' => '10 km south of Example',
                'time' => 1785412800000,
                'title' => $title,
                'url' => 'https://example.test/event/us7000example',
            ],
            'geometry' => [
                'coordinates' => [-3.7, 40.4, 12.5],
            ],
        ]],
    ];
}

test('a selected event updates the import action and can be imported for a selected kaiju', function () {
    $kaiju = Kaiju::factory()->create();

    Http::fakeSequence()
        ->push(usgsImportResponse('Displayed event'))
        ->push(usgsImportResponse('Current event'));

    $component = Livewire::test('pages::usgs.index')
        ->set('selected_event_id', 'us7000example')
        ->assertSet('selected_event_id', 'us7000example')
        ->assertSeeHtml('data-test="selected-usgs-event"')
        ->assertSee('Selected event:')
        ->assertSee('Displayed event')
        ->set('kaiju_id', (string) $kaiju->id)
        ->call('importIncident')
        ->assertHasNoErrors()
        ->assertRedirect(route('incidents.show', Incident::query()->sole()));

    $incident = Incident::query()->sole();

    expect($incident->title)->toBe('Current event')
        ->and($incident->description)->toBe('Imported from USGS: Current event')
        ->and($incident->location)->toBe('10 km south of Example')
        ->and($incident->status)->toBe(IncidentStatus::Open)
        ->and($incident->occurred_at)->toEqual(CarbonImmutable::parse('2026-07-30 12:00:00', 'UTC'))
        ->and($incident->kaiju->is($kaiju))->toBeTrue()
        ->and($incident->source)->toBe('USGS')
        ->and($incident->external_event_id)->toBe('us7000example')
        ->and($incident->external_url)->toBe('https://example.test/event/us7000example')
        ->and($incident->magnitude)->toBe(5.6)
        ->and($incident->latitude)->toBe(40.4)
        ->and($incident->longitude)->toBe(-3.7)
        ->and($incident->depth)->toBe(12.5);

    Http::assertSentCount(2);
});

test('the import action stays disabled until an event is selected', function () {
    Http::fake(['*' => Http::response(usgsImportResponse())]);

    $component = Livewire::test('pages::usgs.index')
        ->assertSeeHtml('wire:model.live="selected_event_id"')
        ->assertSeeHtml('data-test="import-incident"')
        ->assertSeeHtml('disabled="disabled"');

    $component
        ->set('selected_event_id', 'us7000example')
        ->assertSet('selected_event_id', 'us7000example')
        ->assertSeeHtml('data-test="selected-usgs-event"')
        ->assertSee('Selected event:')
        ->assertSee('M 5.6 - 10 km south of Example')
        ->assertDontSeeHtml('disabled="disabled"');
});

test('event and kaiju selections are required before importing', function () {
    Kaiju::factory()->create();
    Http::fake(['*' => Http::response(usgsImportResponse())]);

    Livewire::test('pages::usgs.index')
        ->call('importIncident')
        ->assertHasErrors([
            'selected_event_id' => 'required',
            'kaiju_id' => 'required',
        ]);

    expect(Incident::query()->count())->toBe(0);
    Http::assertSentCount(1);
});

test('an event missing from the current usgs catalogue cannot be imported', function () {
    $kaiju = Kaiju::factory()->create();

    Http::fakeSequence()
        ->push(usgsImportResponse())
        ->push(['type' => 'FeatureCollection', 'features' => []]);

    Livewire::test('pages::usgs.index')
        ->set('selected_event_id', 'us7000example')
        ->set('kaiju_id', (string) $kaiju->id)
        ->call('importIncident')
        ->assertHasErrors(['selected_event_id'])
        ->assertSee('The selected USGS event is no longer available. Please choose another event.');

    expect(Incident::query()->count())->toBe(0);
});

test('an imported incident remains editable through the normal incident form', function () {
    $kaiju = Kaiju::factory()->create();
    $incident = Incident::factory()->for($kaiju)->create([
        'source' => 'USGS',
        'external_event_id' => 'us7000example',
        'external_url' => 'https://example.test/event/us7000example',
        'magnitude' => 5.6,
        'latitude' => 40.4,
        'longitude' => -3.7,
        'depth' => 12.5,
    ]);

    Livewire::test('pages::incidents.edit', ['incident' => $incident])
        ->set('title', 'Updated imported incident')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('incidents.show', $incident));

    expect($incident->fresh())
        ->title->toBe('Updated imported incident')
        ->source->toBe('USGS')
        ->external_event_id->toBe('us7000example')
        ->external_url->toBe('https://example.test/event/us7000example')
        ->magnitude->toBe(5.6)
        ->latitude->toBe(40.4)
        ->longitude->toBe(-3.7)
        ->depth->toBe(12.5);
});
