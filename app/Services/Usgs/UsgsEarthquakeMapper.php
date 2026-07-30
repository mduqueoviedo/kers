<?php

namespace App\Services\Usgs;

use Carbon\CarbonImmutable;

class UsgsEarthquakeMapper
{
    /**
     * Map a USGS GeoJSON response into records suitable for display.
     *
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    public function map(array $payload): array
    {
        $features = $payload['features'] ?? [];

        if (! is_array($features)) {
            return [];
        }

        $events = [];

        foreach ($features as $feature) {
            if (! is_array($feature)) {
                continue;
            }

            $event = $this->mapFeature($feature);

            if ($event !== null) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * @param  array<string, mixed>  $feature
     * @return array<string, mixed>|null
     */
    private function mapFeature(array $feature): ?array
    {
        $properties = $feature['properties'] ?? [];
        $geometry = $feature['geometry'] ?? [];
        $coordinates = $geometry['coordinates'] ?? [];

        if (! is_array($properties) || ! is_array($coordinates)) {
            return null;
        }

        $eventId = $feature['id'] ?? null;
        $timestamp = $properties['time'] ?? null;

        if (! is_string($eventId) || ! is_string($properties['title'] ?? null) || ! is_numeric($timestamp)) {
            return null;
        }

        $occurredAt = CarbonImmutable::createFromTimestampMs((int) $timestamp)
            ->utc();

        return [
            'id' => $eventId,
            'title' => $properties['title'],
            'magnitude' => is_numeric($properties['mag'] ?? null) ? (float) $properties['mag'] : null,
            'location' => is_string($properties['place'] ?? null) ? $properties['place'] : null,
            'occurred_at' => $occurredAt->format('M j, Y, H:i').' UTC',
            'occurred_at_iso' => $occurredAt->toIso8601String(),
            'url' => is_string($properties['url'] ?? null) ? $properties['url'] : null,
            'longitude' => is_numeric($coordinates[0] ?? null) ? (float) $coordinates[0] : null,
            'latitude' => is_numeric($coordinates[1] ?? null) ? (float) $coordinates[1] : null,
            'depth' => is_numeric($coordinates[2] ?? null) ? (float) $coordinates[2] : null,
        ];
    }
}
