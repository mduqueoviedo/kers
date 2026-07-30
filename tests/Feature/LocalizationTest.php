<?php

use App\Models\Incident;
use App\Models\Kaiju;

test('the application defaults to English and renders the locale selector', function () {
    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee('Kaiju registry')
        ->assertSee('English')
        ->assertSee('Spanish')
        ->assertSee('name="locale"', escape: false);
});

test('a selected locale persists during the session', function () {
    $this->post(route('locale.update'), ['locale' => 'es'])
        ->assertRedirect();

    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee('Registro de kaijus')
        ->assertSee('Navegación principal')
        ->assertSee('Español');

    expect(session('locale'))->toBe('es');
});

test('an unsupported locale falls back to English', function () {
    $this->post(route('locale.update'), ['locale' => 'fr'])
        ->assertRedirect();

    $this->get(route('incidents.index'))
        ->assertOk()
        ->assertSee('Incident operations')
        ->assertSee('English')
        ->assertDontSee('Operaciones de incidentes');

    expect(session('locale'))->toBe('en');
});

test('Spanish renders translated Kaiju and Incident catalogue copy without changing domain data', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Stormwing']);
    Incident::factory()->for($kaiju)->create(['title' => 'Harbour alert']);

    $this->withSession(['locale' => 'es'])
        ->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee('Catálogo de kaijus')
        ->assertSee('Stormwing');

    $this->withSession(['locale' => 'es'])
        ->get(route('incidents.index'))
        ->assertOk()
        ->assertSee('Catálogo de incidentes')
        ->assertSee('Harbour alert');
});

test('English and Spanish authentication translations have matching keys', function () {
    $english = require lang_path('en/auth.php');
    $spanish = require lang_path('es/auth.php');

    expect(array_keys($spanish))->toBe(array_keys($english));
});
