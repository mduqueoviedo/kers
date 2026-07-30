<?php

use App\Services\Usgs\UsgsEarthquakeMapper;

test('it maps the fields needed to display a usgs earthquake event', function () {
    $events = app(UsgsEarthquakeMapper::class)->map([
        'type' => 'FeatureCollection',
        'features' => [[
            'id' => 'us7000example',
            'properties' => [
                'mag' => 5.6,
                'place' => '10 km south of Example',
                'time' => 1785412800000,
                'title' => 'M 5.6 - 10 km south of Example',
                'url' => 'https://example.test/event/us7000example',
            ],
            'geometry' => [
                'coordinates' => [-3.7, 40.4, 12.5],
            ],
        ]],
    ]);

    expect($events)->toHaveCount(1)
        ->and($events[0])->toMatchArray([
            'id' => 'us7000example',
            'title' => 'M 5.6 - 10 km south of Example',
            'magnitude' => 5.6,
            'location' => '10 km south of Example',
            'url' => 'https://example.test/event/us7000example',
            'longitude' => -3.7,
            'latitude' => 40.4,
            'depth' => 12.5,
        ])
        ->and($events[0]['occurred_at'])->toBe('Jul 30, 2026, 12:00 UTC')
        ->and($events[0]['occurred_at_iso'])->toBe('2026-07-30T12:00:00+00:00');
});

test('it ignores features without the required display fields', function () {
    $events = app(UsgsEarthquakeMapper::class)->map([
        'features' => [
            ['id' => 'missing-properties'],
            [
                'id' => 'missing-time',
                'properties' => ['title' => 'Missing time'],
                'geometry' => ['coordinates' => []],
            ],
        ],
    ]);

    expect($events)->toBe([]);
});
