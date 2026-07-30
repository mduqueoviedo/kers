<?php

use Illuminate\Support\Facades\Http;

function fakeUsgsEventsResponse(): void
{
    Http::fake([
        '*' => Http::response([
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
        ]),
    ]);
}

test('guests can view mapped recent usgs events', function () {
    fakeUsgsEventsResponse();

    $this->get(route('usgs.index'))
        ->assertOk()
        ->assertSee('Recent seismic events')
        ->assertSee('M 5.6 - 10 km south of Example')
        ->assertSee('10 km south of Example')
        ->assertSee('5.6')
        ->assertSee('July 30, 2026 12:00 PM UTC')
        ->assertSee('View USGS details')
        ->assertSee('USGS events');

    Http::assertSent(fn ($request) => str_contains($request->url(), 'format=geojson'));
});

test('the usgs page displays a translated failure state', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Unavailable'], 503),
    ]);

    $this->withSession(['locale' => 'es'])
        ->get(route('usgs.index'))
        ->assertOk()
        ->assertSee('Los datos de USGS no están disponibles')
        ->assertSee('No se han podido obtener los últimos eventos sísmicos. Inténtalo más tarde.');
});
