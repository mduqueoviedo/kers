<?php

test('kaiju routes show the active registry context', function () {
    $response = $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee('Kaiju registry')
        ->assertSee('Known creature records')
        ->assertDontSee('Incident operations')
        ->assertSee('bg-teal-100', escape: false)
        ->assertSee('src="'.asset('favicon.svg').'"', escape: false);

    expect(substr_count($response->getContent(), 'aria-current="page"'))->toBe(1);
});

test('incident routes show the active operations context', function () {
    $response = $this->get(route('incidents.index'))
        ->assertOk()
        ->assertSee('Incident operations')
        ->assertSee('Recorded emergency activity')
        ->assertDontSee('Kaiju registry')
        ->assertSee('bg-orange-100', escape: false)
        ->assertSee('src="'.asset('favicon.svg').'"', escape: false);

    expect(substr_count($response->getContent(), 'aria-current="page"'))->toBe(1);
});

test('usgs routes show the active seismic context', function () {
    $response = $this->get(route('usgs.index'))
        ->assertOk()
        ->assertSee('Seismic intelligence')
        ->assertSee('Live external event data')
        ->assertDontSee('Kaiju registry')
        ->assertDontSee('Incident operations')
        ->assertSee('bg-sky-100', escape: false);

    expect(substr_count($response->getContent(), 'aria-current="page"'))->toBe(1);
});
