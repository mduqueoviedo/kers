<?php

use App\Models\Incident;
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
    $incident = Incident::factory()->for($kaiju)->create();

    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->call('requestDeletion')
        ->call('cancelDeletion')
        ->assertSet('confirmingDeletion', false);

    $this->assertDatabaseHas('kaijus', ['id' => $kaiju->id]);
    $this->assertDatabaseHas('incidents', ['id' => $incident->id]);
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
    $incident = Incident::factory()->for($kaiju)->create();
    $otherIncident = Incident::factory()->for($otherKaiju)->create();

    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->call('requestDeletion')
        ->call('deleteKaiju')
        ->assertDispatched('toast-show')
        ->assertRedirect(route('kaijus.index'));

    $this->assertDatabaseMissing('kaijus', ['id' => $kaiju->id]);
    $this->assertDatabaseMissing('incidents', ['id' => $incident->id]);
    $this->assertDatabaseHas('kaijus', ['id' => $otherKaiju->id]);
    $this->assertDatabaseHas('incidents', ['id' => $otherIncident->id]);
});

test('the deletion warning reports the exact incident count', function (
    int $incidentCount,
    string $countMessage,
    ?string $cascadeMessage,
) {
    $kaiju = Kaiju::factory()->create();

    if ($incidentCount > 0) {
        Incident::factory()
            ->count($incidentCount)
            ->for($kaiju)
            ->create();
    }

    $component = Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->call('requestDeletion')
        ->assertSet('confirmingDeletion', true)
        ->assertSee($countMessage);

    if ($cascadeMessage === null) {
        $component->assertDontSee('Deleting it will also permanently delete');

        return;
    }

    $component->assertSee($cascadeMessage);
})->with([
    'no incidents' => [
        0,
        'This kaiju has 0 associated incidents.',
        null,
    ],
    'one incident' => [
        1,
        'This kaiju has 1 associated incident.',
        'Deleting it will also permanently delete that incident.',
    ],
    'multiple incidents' => [
        3,
        'This kaiju has 3 associated incidents.',
        'Deleting it will also permanently delete those incidents.',
    ],
]);
