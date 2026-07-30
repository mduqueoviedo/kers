<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('authenticated users can view a prefilled kaiju edit form', function () {
    $kaiju = Kaiju::factory()->create([
        'name' => 'Leviathan',
        'category' => KaijuCategory::Aquatic,
        'threat_level' => 5,
        'description' => 'A colossal creature detected beneath the Atlantic.',
    ]);

    $this->get(route('kaijus.edit', $kaiju))
        ->assertOk()
        ->assertSee('Edit kaiju');

    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])
        ->assertSet('kaiju.id', $kaiju->id)
        ->assertSet('name', 'Leviathan')
        ->assertSet('category', KaijuCategory::Aquatic->value)
        ->assertSet('threat_level', '5')
        ->assertSet('description', 'A colossal creature detected beneath the Atlantic.');
});

test('a valid kaiju edit is persisted', function () {
    $createdAt = Carbon::parse('2026-01-15 14:30:00');
    $updatedAt = Carbon::parse('2026-03-20 09:45:00');
    $kaiju = Kaiju::factory()->create([
        'name' => 'Leviathan',
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);

    $this->travelTo($updatedAt);

    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])
        ->set('name', 'Leviathan Prime')
        ->set('category', KaijuCategory::Amphibious->value)
        ->set('threat_level', '4')
        ->set('description', 'Now active along the Atlantic coastline.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('toast-show')
        ->assertRedirect(route('kaijus.show', $kaiju));

    $kaiju->refresh();

    expect($kaiju)
        ->name->toBe('Leviathan Prime')
        ->category->toBe(KaijuCategory::Amphibious)
        ->threat_level->toBe(4)
        ->description->toBe('Now active along the Atlantic coastline.')
        ->and($kaiju->created_at?->equalTo($createdAt))->toBeTrue()
        ->and($kaiju->updated_at?->equalTo($updatedAt))->toBeTrue();
});

test('a kaiju can retain its current name', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Leviathan']);

    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('kaijus.show', $kaiju));

    $this->assertDatabaseHas('kaijus', [
        'id' => $kaiju->id,
        'name' => 'Leviathan',
    ]);
});

test('an exact name belonging to another kaiju is rejected', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Leviathan']);
    Kaiju::factory()->create(['name' => 'Stormwing']);

    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])
        ->set('name', 'Stormwing')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);

    $this->assertDatabaseHas('kaijus', [
        'id' => $kaiju->id,
        'name' => 'Leviathan',
    ]);
});

test('required fields are validated before updating a kaiju', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Leviathan']);

    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])
        ->set('name', '')
        ->set('category', '')
        ->set('threat_level', '')
        ->call('save')
        ->assertHasErrors([
            'name' => 'required',
            'category' => 'required',
            'threat_level' => 'required',
        ]);

    expect($kaiju->fresh()?->name)->toBe('Leviathan');
});

test('the edited name cannot exceed the database column length', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Leviathan']);

    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])
        ->set('name', str_repeat('K', 256))
        ->call('save')
        ->assertHasErrors(['name' => 'max']);

    expect($kaiju->fresh()?->name)->toBe('Leviathan');
});

test('invalid category and threat levels do not update a kaiju', function (
    string $field,
    string $value,
) {
    $kaiju = Kaiju::factory()->create([
        'name' => 'Leviathan',
        'category' => KaijuCategory::Aquatic,
        'threat_level' => 5,
    ]);

    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])
        ->set($field, $value)
        ->call('save')
        ->assertHasErrors([$field]);

    $kaiju->refresh();

    expect($kaiju->category)->toBe(KaijuCategory::Aquatic)
        ->and($kaiju->threat_level)->toBe(5);
})->with([
    'unsupported category' => ['category', 'cosmic'],
    'threat level below the minimum' => ['threat_level', '0'],
    'threat level above the maximum' => ['threat_level', '6'],
    'non-integer threat level' => ['threat_level', 'high'],
]);

test('clearing the description stores null', function () {
    $kaiju = Kaiju::factory()->create([
        'description' => 'Previously documented observations.',
    ]);

    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])
        ->set('description', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($kaiju->fresh()?->description)->toBeNull();
});

test('an unknown kaiju edit page returns not found', function () {
    $this->get(route('kaijus.edit', ['kaiju' => 999_999]))
        ->assertNotFound();
});

test('the detail page links to its kaiju edit form', function () {
    $kaiju = Kaiju::factory()->create();

    $this->get(route('kaijus.show', $kaiju))
        ->assertOk()
        ->assertSee(route('kaijus.edit', $kaiju), escape: false);
});
