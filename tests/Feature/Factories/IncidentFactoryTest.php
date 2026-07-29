<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;

test('the incident factory creates a valid related incident in UTC', function () {
    $incident = Incident::factory()->create();

    expect($incident->title)->not->toBeEmpty()
        ->and($incident->description)->not->toBeEmpty()
        ->and($incident->location)->not->toBeEmpty()
        ->and($incident->status)->toBeInstanceOf(IncidentStatus::class)
        ->and($incident->occurred_at)->toBeInstanceOf(CarbonImmutable::class)
        ->and($incident->occurred_at->timezoneName)->toBe('UTC')
        ->and($incident->kaiju)->toBeInstanceOf(Kaiju::class);

    $this->assertDatabaseHas('incidents', [
        'id' => $incident->id,
        'kaiju_id' => $incident->kaiju->id,
    ]);
});

test('the incident factory can use an existing kaiju', function () {
    $kaiju = Kaiju::factory()->create();

    $incident = Incident::factory()
        ->for($kaiju)
        ->create();

    expect($incident->kaiju->is($kaiju))->toBeTrue();

    $this->assertDatabaseCount('kaijus', 1);
    $this->assertDatabaseHas('incidents', [
        'id' => $incident->id,
        'kaiju_id' => $kaiju->id,
    ]);
});

test('the incident factory provides each lifecycle state', function () {
    $openIncident = Incident::factory()->open()->create();
    $containedIncident = Incident::factory()->contained()->create();
    $closedIncident = Incident::factory()->closed()->create();

    expect($openIncident->status)->toBe(IncidentStatus::Open)
        ->and($containedIncident->status)->toBe(IncidentStatus::Contained)
        ->and($closedIncident->status)->toBe(IncidentStatus::Closed);
});
