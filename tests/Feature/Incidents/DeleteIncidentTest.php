<?php

use App\Models\Incident;
use App\Models\Kaiju;
use Livewire\Livewire;

test('guests can request confirmation before deleting an incident', function () {
    $incident = Incident::factory()->create(['title' => 'Harbour evacuation']);

    Livewire::test('pages::incidents.show', ['incident' => $incident])
        ->assertSet('confirmingDeletion', false)
        ->assertSee('Delete incident')
        ->call('requestDeletion')
        ->assertSet('confirmingDeletion', true)
        ->assertSee('Are you sure you want to delete Harbour evacuation? This action cannot be undone.');

    $this->assertDatabaseHas('incidents', ['id' => $incident->id]);
});

test('incident deletion can be cancelled', function () {
    $incident = Incident::factory()->create();

    Livewire::test('pages::incidents.show', ['incident' => $incident])
        ->call('requestDeletion')
        ->call('cancelDeletion')
        ->assertSet('confirmingDeletion', false);

    $this->assertDatabaseHas('incidents', ['id' => $incident->id]);
});

test('an incident cannot be deleted without confirmation', function () {
    $incident = Incident::factory()->create();

    Livewire::test('pages::incidents.show', ['incident' => $incident])
        ->call('deleteIncident')
        ->assertSet('confirmingDeletion', false);

    $this->assertDatabaseHas('incidents', ['id' => $incident->id]);
});

test('confirming deletion removes only the selected incident and preserves its kaiju', function () {
    $kaiju = Kaiju::factory()->create();
    $incident = Incident::factory()->for($kaiju)->create();
    $otherIncident = Incident::factory()->for($kaiju)->create();

    Livewire::test('pages::incidents.show', ['incident' => $incident])
        ->call('requestDeletion')
        ->call('deleteIncident')
        ->assertDispatched('toast-show')
        ->assertRedirect(route('incidents.index'));

    $this->assertDatabaseMissing('incidents', ['id' => $incident->id]);
    $this->assertDatabaseHas('incidents', ['id' => $otherIncident->id]);
    $this->assertDatabaseHas('kaijus', ['id' => $kaiju->id]);
});
