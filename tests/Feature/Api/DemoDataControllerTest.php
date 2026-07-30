<?php

use App\Models\Incident;
use App\Models\Kaiju;
use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Database\Seeders\IncidentSeeder;
use Database\Seeders\KaijuSeeder;

beforeEach(function () {
    config()->set('kers.demo_api_key', 'test-demo-api-key');
});

test('the demo data reset route is unavailable when no api key is configured', function () {
    config()->set('kers.demo_api_key', '');

    $this->withToken('test-demo-api-key')
        ->postJson('/api/demo-data/reset')
        ->assertNotFound();
});

test('missing or invalid api keys cannot modify demo data', function (?string $providedKey) {
    $kaiju = Kaiju::factory()->create();
    Incident::factory()->for($kaiju)->create();

    if ($providedKey !== null) {
        $this->withToken($providedKey);
    }

    $this->postJson('/api/demo-data/reset')
        ->assertUnauthorized()
        ->assertJson(['message' => 'Invalid demo API key.']);

    $this->assertDatabaseHas('kaijus', ['id' => $kaiju->id]);
    $this->assertDatabaseCount('incidents', 1);
})->with([
    'missing key' => [null],
    'invalid key' => ['invalid-demo-api-key'],
]);

test('api keys are not accepted in query parameters', function () {
    $kaiju = Kaiju::factory()->create();

    $this->postJson('/api/demo-data/reset?api_key=test-demo-api-key')
        ->assertUnauthorized();

    $this->assertDatabaseHas('kaijus', ['id' => $kaiju->id]);
});

test('the reset route restores canonical domain data and preserves users', function () {
    $this->seed([DemoUserSeeder::class, KaijuSeeder::class, IncidentSeeder::class]);

    $modifiedKaiju = Kaiju::query()->where('name', 'Abyssal Maw')->firstOrFail();
    $modifiedKaiju->update(['name' => 'Edited Abyssal Maw']);

    $additionalKaiju = Kaiju::factory()->create(['name' => 'Temporary Kaiju']);
    Incident::factory()->for($additionalKaiju)->create();
    $additionalUser = User::factory()->create();

    $this->withToken('test-demo-api-key')
        ->postJson('/api/demo-data/reset')
        ->assertOk()
        ->assertExactJson([
            'message' => 'Demo data reset.',
            'records' => [
                'kaijus' => 12,
                'incidents' => 9,
            ],
        ]);

    $this->assertDatabaseCount('kaijus', 12);
    $this->assertDatabaseCount('incidents', 9);
    $this->assertDatabaseMissing('kaijus', ['id' => $modifiedKaiju->id]);
    $this->assertDatabaseMissing('kaijus', ['name' => 'Temporary Kaiju']);
    $this->assertDatabaseHas('kaijus', ['name' => 'Abyssal Maw']);
    $this->assertDatabaseHas('users', ['email' => config()->string('kers.demo_user.email')]);
    $this->assertDatabaseHas('users', ['id' => $additionalUser->id]);
});

test('the old demo data endpoints are unavailable', function (string $method, string $path) {
    $this->withToken('test-demo-api-key')
        ->{$method}($path)
        ->assertNotFound();
})->with([
    'wipe route' => ['deleteJson', '/api/demo-data'],
    'seed route' => ['postJson', '/api/demo-data/seed'],
]);
