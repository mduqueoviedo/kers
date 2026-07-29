<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Livewire\Livewire;

test('guests can view the kaiju registration form', function () {
    $this->get(route('kaijus.create'))
        ->assertOk()
        ->assertSee('Register kaiju')
        ->assertSee('Name')
        ->assertSee('Category')
        ->assertSee('Threat level')
        ->assertSee('Description');
});

test('a valid kaiju can be registered', function () {
    Livewire::test('pages::kaijus.create')
        ->set('name', 'Leviathan')
        ->set('category', KaijuCategory::Aquatic->value)
        ->set('threat_level', '5')
        ->set('description', 'A colossal creature detected beneath the Atlantic.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('toast-show')
        ->assertRedirect(route('kaijus.index'));

    $this->assertDatabaseHas('kaijus', [
        'name' => 'Leviathan',
        'category' => KaijuCategory::Aquatic->value,
        'threat_level' => 5,
        'description' => 'A colossal creature detected beneath the Atlantic.',
    ]);

    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee('Leviathan');
});

test('the description is optional', function () {
    Livewire::test('pages::kaijus.create')
        ->set('name', 'Stoneback')
        ->set('category', KaijuCategory::Terrestrial->value)
        ->set('threat_level', '2')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('kaijus', [
        'name' => 'Stoneback',
        'description' => null,
    ]);
});

test('required fields are validated', function () {
    Livewire::test('pages::kaijus.create')
        ->call('save')
        ->assertHasErrors([
            'name' => 'required',
            'category' => 'required',
            'threat_level' => 'required',
        ]);

    $this->assertDatabaseCount('kaijus', 0);
});

test('the name cannot exceed the database column length', function () {
    Livewire::test('pages::kaijus.create')
        ->set('name', str_repeat('K', 256))
        ->set('category', KaijuCategory::Unknown->value)
        ->set('threat_level', '1')
        ->call('save')
        ->assertHasErrors(['name' => 'max']);

    $this->assertDatabaseCount('kaijus', 0);
});

test('the category must be a supported enum value', function () {
    Livewire::test('pages::kaijus.create')
        ->set('name', 'Voidwalker')
        ->set('category', 'cosmic')
        ->set('threat_level', '4')
        ->call('save')
        ->assertHasErrors(['category']);

    $this->assertDatabaseCount('kaijus', 0);
});

test('the threat level must be within the supported range', function (string $threatLevel) {
    Livewire::test('pages::kaijus.create')
        ->set('name', 'Unrated creature')
        ->set('category', KaijuCategory::Unknown->value)
        ->set('threat_level', $threatLevel)
        ->call('save')
        ->assertHasErrors(['threat_level']);

    $this->assertDatabaseCount('kaijus', 0);
})->with([
    'below the minimum' => '0',
    'above the maximum' => '6',
    'not an integer' => 'high',
]);

test('an exact duplicate name is rejected', function () {
    Kaiju::factory()->create(['name' => 'Leviathan']);

    Livewire::test('pages::kaijus.create')
        ->set('name', 'Leviathan')
        ->set('category', KaijuCategory::Aerial->value)
        ->set('threat_level', '3')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);

    $this->assertDatabaseCount('kaijus', 1);
});
