<?php

use App\Models\Kaiju;
use Livewire\Livewire;

test('guests can request confirmation before deleting a kaiju', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Leviathan']);

    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->assertSet('confirmingDeletion', false)
        ->assertSee('Delete kaiju')
        ->call('requestDeletion')
        ->assertSet('confirmingDeletion', true)
        ->assertSee('Are you sure you want to delete Leviathan? This action cannot be undone.');

    $this->assertDatabaseHas('kaijus', ['id' => $kaiju->id]);
});

test('deletion can be cancelled', function () {
    $kaiju = Kaiju::factory()->create();

    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->call('requestDeletion')
        ->call('cancelDeletion')
        ->assertSet('confirmingDeletion', false);

    $this->assertDatabaseHas('kaijus', ['id' => $kaiju->id]);
});

test('a kaiju cannot be deleted without confirmation', function () {
    $kaiju = Kaiju::factory()->create();

    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->call('deleteKaiju')
        ->assertSet('confirmingDeletion', false);

    $this->assertDatabaseHas('kaijus', ['id' => $kaiju->id]);
});

test('confirming deletion removes only the selected kaiju', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Leviathan']);
    $otherKaiju = Kaiju::factory()->create(['name' => 'Stormwing']);

    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->call('requestDeletion')
        ->call('deleteKaiju')
        ->assertDispatched('toast-show')
        ->assertRedirect(route('kaijus.index'));

    $this->assertDatabaseMissing('kaijus', ['id' => $kaiju->id]);
    $this->assertDatabaseHas('kaijus', ['id' => $otherKaiju->id]);
});
