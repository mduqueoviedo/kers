<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

test('an incident can be persisted with its casts and relationships', function () {
    $kaiju = Kaiju::factory()->create();
    $occurredAt = CarbonImmutable::parse('2026-07-29 12:30:00', 'UTC');

    $incident = Incident::query()->create([
        'title' => 'Atlantic coastal breach',
        'description' => 'Leviathan surfaced near a major shipping route.',
        'location' => 'North Atlantic Ocean',
        'status' => IncidentStatus::Open,
        'occurred_at' => $occurredAt,
        'kaiju_id' => $kaiju->id,
    ])->refresh();

    expect($incident->title)->toBe('Atlantic coastal breach')
        ->and($incident->description)->toBe('Leviathan surfaced near a major shipping route.')
        ->and($incident->location)->toBe('North Atlantic Ocean')
        ->and($incident->status)->toBe(IncidentStatus::Open)
        ->and($incident->occurred_at)->toBeInstanceOf(CarbonImmutable::class)
        ->and($incident->occurred_at->equalTo($occurredAt))->toBeTrue()
        ->and($incident->occurred_at->timezoneName)->toBe('UTC')
        ->and($incident->kaiju->is($kaiju))->toBeTrue()
        ->and($kaiju->incidents()->first()?->is($incident))->toBeTrue();

    $this->assertDatabaseHas('incidents', [
        'id' => $incident->id,
        'status' => IncidentStatus::Open->value,
        'kaiju_id' => $kaiju->id,
    ]);
});

test('the database accepts every incident status', function (IncidentStatus $status) {
    $incident = Incident::query()->create([
        'title' => 'Status test incident',
        'description' => 'An incident used to verify the status constraint.',
        'location' => 'Test sector',
        'status' => $status,
        'occurred_at' => CarbonImmutable::parse('2026-07-29 12:30:00', 'UTC'),
        'kaiju_id' => Kaiju::factory()->create()->id,
    ])->refresh();

    expect($incident->status)->toBe($status);
})->with(IncidentStatus::cases());

test('the database rejects an invalid incident status', function () {
    $kaiju = Kaiju::factory()->create();

    expect(fn () => DB::table('incidents')->insert([
        'title' => 'Invalid status incident',
        'description' => 'This incident must not be stored.',
        'location' => 'Test sector',
        'status' => 'monitoring',
        'occurred_at' => '2026-07-29 12:30:00',
        'kaiju_id' => $kaiju->id,
    ]))->toThrow(QueryException::class);
});

test('the database rejects null required incident fields', function (string $field) {
    $data = [
        'title' => 'Incomplete incident',
        'description' => 'This incident is missing a required field.',
        'location' => 'Test sector',
        'status' => IncidentStatus::Open->value,
        'occurred_at' => '2026-07-29 12:30:00',
        'kaiju_id' => Kaiju::factory()->create()->id,
    ];
    $data[$field] = null;

    expect(fn () => DB::table('incidents')->insert($data))
        ->toThrow(QueryException::class);
})->with([
    'title',
    'description',
    'location',
    'status',
    'occurred_at',
    'kaiju_id',
]);

test('the database rejects an incident for an unknown kaiju', function () {
    expect(fn () => DB::table('incidents')->insert([
        'title' => 'Unlinked incident',
        'description' => 'This incident references an unknown creature.',
        'location' => 'Test sector',
        'status' => IncidentStatus::Open->value,
        'occurred_at' => '2026-07-29 12:30:00',
        'kaiju_id' => 999_999,
    ]))->toThrow(QueryException::class);
});

test('deleting a kaiju cascades only to its incidents', function () {
    $kaiju = Kaiju::factory()->create();
    $otherKaiju = Kaiju::factory()->create();

    $incident = Incident::query()->create([
        'title' => 'Selected kaiju incident',
        'description' => 'This incident should be deleted by the cascade.',
        'location' => 'Test sector',
        'status' => IncidentStatus::Open,
        'occurred_at' => CarbonImmutable::parse('2026-07-29 12:30:00', 'UTC'),
        'kaiju_id' => $kaiju->id,
    ]);
    $otherIncident = Incident::query()->create([
        'title' => 'Other kaiju incident',
        'description' => 'This incident must remain stored.',
        'location' => 'Other sector',
        'status' => IncidentStatus::Contained,
        'occurred_at' => CarbonImmutable::parse('2026-07-29 13:30:00', 'UTC'),
        'kaiju_id' => $otherKaiju->id,
    ]);

    $kaiju->delete();

    $this->assertDatabaseMissing('incidents', ['id' => $incident->id]);
    $this->assertDatabaseHas('incidents', ['id' => $otherIncident->id]);
    $this->assertDatabaseHas('kaijus', ['id' => $otherKaiju->id]);
});
