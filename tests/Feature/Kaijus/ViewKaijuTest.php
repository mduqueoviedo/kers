<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('guests can view the correct kaiju details', function () {
    $createdAt = Carbon::parse('2026-01-15 14:30:00');
    $updatedAt = Carbon::parse('2026-02-20 09:45:00');

    $kaiju = Kaiju::factory()->create([
        'name' => 'Leviathan',
        'category' => KaijuCategory::Aquatic,
        'threat_level' => 5,
        'description' => 'A colossal creature detected beneath the Atlantic.',
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ]);

    Kaiju::factory()->create([
        'name' => 'Stormwing',
    ]);

    $this->get(route('kaijus.show', $kaiju))
        ->assertOk()
        ->assertSee('Leviathan')
        ->assertSee('Aquatic')
        ->assertSee('Level 5 of 5')
        ->assertSee('A colossal creature detected beneath the Atlantic.')
        ->assertSee($createdAt->isoFormat('LLL'))
        ->assertSee($updatedAt->isoFormat('LLL'))
        ->assertSee($createdAt->toIso8601String())
        ->assertSee($updatedAt->toIso8601String())
        ->assertDontSee($createdAt->toDateTimeString())
        ->assertDontSee($updatedAt->toDateTimeString())
        ->assertDontSee('Stormwing');
});

test('the detail component displays the optional description fallback', function () {
    $kaiju = Kaiju::factory()->withoutDescription()->create([
        'name' => 'Frostveil',
    ]);

    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])
        ->assertSet('kaiju.id', $kaiju->id)
        ->assertSee('Frostveil')
        ->assertSee('No description provided.');
});

test('an unknown kaiju returns not found', function () {
    $this->get(route('kaijus.show', ['kaiju' => 999_999]))
        ->assertNotFound();
});

test('catalogue cards link to their own detail pages', function () {
    $firstKaiju = Kaiju::factory()->create(['name' => 'Abyssal Maw']);
    $secondKaiju = Kaiju::factory()->create(['name' => 'Stormwing']);

    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertSee(route('kaijus.show', $firstKaiju), escape: false)
        ->assertSee(route('kaijus.show', $secondKaiju), escape: false);
});
