<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;

test('the database seeder creates repeatable representative incidents', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Incident::query()->count())->toBe(9);

    $expectedIncidents = [
        ['Atlantic Shipping Route Breach', 'Abyssal Maw', IncidentStatus::Open, '2026-07-29 06:00:00'],
        ['Deep-Sea Sonar Blackout', 'Abyssal Maw', IncidentStatus::Contained, '2026-07-24 18:30:00'],
        ['Volcanic Tunnel Evacuation', 'Cinderhorn', IncidentStatus::Contained, '2026-07-22 09:15:00'],
        ['Storm Corridor Airspace Closure', 'Cloudtalon', IncidentStatus::Open, '2026-07-27 14:45:00'],
        ['Lava Lagoon Containment', 'Emberfin', IncidentStatus::Closed, '2026-07-12 03:20:00'],
        ['Unidentified Arctic Signal', 'Frostveil', IncidentStatus::Open, '2026-07-28 23:10:00'],
        ['Fault Line Infrastructure Collapse', 'Graniteback', IncidentStatus::Contained, '2026-07-19 16:05:00'],
        ['High-Altitude Downdraft Emergency', 'Skybreaker', IncidentStatus::Closed, '2026-07-08 11:40:00'],
        ['Coastal Surge Warning', 'Tidecaller', IncidentStatus::Open, '2026-07-26 04:50:00'],
    ];

    foreach ($expectedIncidents as [$title, $kaijuName, $status, $occurredAt]) {
        $incident = Incident::query()
            ->where('title', $title)
            ->firstOrFail();

        expect($incident->kaiju->name)->toBe($kaijuName)
            ->and($incident->status)->toBe($status)
            ->and($incident->occurred_at)->toBeInstanceOf(CarbonImmutable::class)
            ->and($incident->occurred_at->timezoneName)->toBe('UTC')
            ->and($incident->occurred_at->toDateTimeString())->toBe($occurredAt);
    }

    expect(Incident::query()->where('status', IncidentStatus::Open)->count())->toBe(4)
        ->and(Incident::query()->where('status', IncidentStatus::Contained)->count())->toBe(3)
        ->and(Incident::query()->where('status', IncidentStatus::Closed)->count())->toBe(2);
});
