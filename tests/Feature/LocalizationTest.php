<?php

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
