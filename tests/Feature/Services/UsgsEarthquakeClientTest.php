<?php

use App\Services\Usgs\UsgsEarthquakeClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

test('it fetches recent events as geojson from usgs', function () {
    config()->set('services.usgs.url', 'https://example.test/usgs');
    config()->set('services.usgs.timeout', 7);
    config()->set('services.usgs.limit', 15);

    Http::fake([
        'https://example.test/usgs*' => Http::response([
            'type' => 'FeatureCollection',
            'features' => [
                ['id' => 'event-1'],
            ],
        ]),
    ]);

    $events = app(UsgsEarthquakeClient::class)->fetchRecentEvents();

    expect($events)->toBe([
        'type' => 'FeatureCollection',
        'features' => [
            ['id' => 'event-1'],
        ],
    ]);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://example.test/usgs?format=geojson&orderby=time&limit=15'
            && $request->method() === 'GET';
    });
});

test('it throws a request exception when usgs returns an error', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Unavailable'], 503),
    ]);

    expect(fn () => app(UsgsEarthquakeClient::class)->fetchRecentEvents())
        ->toThrow(RequestException::class);
});

test('it throws a connection exception when usgs cannot be reached', function () {
    Http::fake(function () {
        throw new ConnectionException('USGS connection failed.');
    });

    expect(fn () => app(UsgsEarthquakeClient::class)->fetchRecentEvents())
        ->toThrow(ConnectionException::class);
});
