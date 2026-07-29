<?php

namespace Database\Seeders;

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    /**
     * Seed representative incidents for local development.
     */
    public function run(): void
    {
        $incidents = [
            [
                'kaiju' => 'Abyssal Maw',
                'title' => 'Atlantic Shipping Route Breach',
                'description' => 'Abyssal Maw surfaced beneath a commercial shipping corridor.',
                'location' => 'North Atlantic Ocean',
                'status' => IncidentStatus::Open,
                'occurred_at' => '2026-07-29 06:00:00',
            ],
            [
                'kaiju' => 'Abyssal Maw',
                'title' => 'Deep-Sea Sonar Blackout',
                'description' => 'Regional sonar stations lost contact after a deep-water movement.',
                'location' => 'Mid-Atlantic Ridge',
                'status' => IncidentStatus::Contained,
                'occurred_at' => '2026-07-24 18:30:00',
            ],
            [
                'kaiju' => 'Cinderhorn',
                'title' => 'Volcanic Tunnel Evacuation',
                'description' => 'Superheated tunnels forced an evacuation around the volcano.',
                'location' => 'Mount Aso, Japan',
                'status' => IncidentStatus::Contained,
                'occurred_at' => '2026-07-22 09:15:00',
            ],
            [
                'kaiju' => 'Cloudtalon',
                'title' => 'Storm Corridor Airspace Closure',
                'description' => 'Cloudtalon entered an active storm system near civilian air routes.',
                'location' => 'Philippine Sea',
                'status' => IncidentStatus::Open,
                'occurred_at' => '2026-07-27 14:45:00',
            ],
            [
                'kaiju' => 'Emberfin',
                'title' => 'Lava Lagoon Containment',
                'description' => 'Emergency barriers redirected Emberfin away from island settlements.',
                'location' => 'Hawaiian Islands',
                'status' => IncidentStatus::Closed,
                'occurred_at' => '2026-07-12 03:20:00',
            ],
            [
                'kaiju' => 'Frostveil',
                'title' => 'Unidentified Arctic Signal',
                'description' => 'A moving seismic signal was detected beneath polar ice.',
                'location' => 'Arctic Ocean',
                'status' => IncidentStatus::Open,
                'occurred_at' => '2026-07-28 23:10:00',
            ],
            [
                'kaiju' => 'Graniteback',
                'title' => 'Fault Line Infrastructure Collapse',
                'description' => 'Graniteback emerged beneath a transport corridor along the fault.',
                'location' => 'San Andreas Fault, USA',
                'status' => IncidentStatus::Contained,
                'occurred_at' => '2026-07-19 16:05:00',
            ],
            [
                'kaiju' => 'Skybreaker',
                'title' => 'High-Altitude Downdraft Emergency',
                'description' => 'Violent downdrafts disrupted aviation across the monitored sector.',
                'location' => 'Tasman Sea',
                'status' => IncidentStatus::Closed,
                'occurred_at' => '2026-07-08 11:40:00',
            ],
            [
                'kaiju' => 'Tidecaller',
                'title' => 'Coastal Surge Warning',
                'description' => 'Tidecaller movements generated a destructive surge toward the coast.',
                'location' => 'Bay of Bengal',
                'status' => IncidentStatus::Open,
                'occurred_at' => '2026-07-26 04:50:00',
            ],
        ];

        foreach ($incidents as $incident) {
            $kaiju = Kaiju::query()
                ->where('name', $incident['kaiju'])
                ->firstOrFail();

            Incident::query()->updateOrCreate(
                [
                    'title' => $incident['title'],
                    'kaiju_id' => $kaiju->id,
                ],
                [
                    'description' => $incident['description'],
                    'location' => $incident['location'],
                    'status' => $incident['status'],
                    'occurred_at' => CarbonImmutable::parse($incident['occurred_at'], 'UTC'),
                ],
            );
        }
    }
}
