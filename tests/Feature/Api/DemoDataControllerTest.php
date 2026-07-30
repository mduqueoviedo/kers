<?php

use App\Models\Incident;
use App\Models\Kaiju;
use App\Models\User;

beforeEach(function () {
    config()->set('kers.demo_api_key', 'test-demo-api-key');
});

test('demo data routes are unavailable when no api key is configured', function () {
    config()->set('kers.demo_api_key', '');

    $this->withToken('test-demo-api-key')
        ->deleteJson('/api/demo-data')
        ->assertNotFound();

    $this->withToken('test-demo-api-key')
        ->postJson('/api/demo-data/seed')
        ->assertNotFound();
});

test('missing or invalid api keys cannot modify demo data', function (?string $providedKey) {
    $kaiju = Kaiju::factory()->create();
    Incident::factory()->for($kaiju)->create();

    if ($providedKey !== null) {
        $this->withToken($providedKey);
    }

    $this->deleteJson('/api/demo-data')
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

    $this->deleteJson('/api/demo-data?api_key=test-demo-api-key')
        ->assertUnauthorized();

    $this->assertDatabaseHas('kaijus', ['id' => $kaiju->id]);
});

test('the wipe route deletes only current domain data', function () {
    $user = User::factory()->create();
    $kaijus = Kaiju::factory()->count(2)->create();

    Incident::factory()->count(2)->for($kaijus[0])->create();
    Incident::factory()->for($kaijus[1])->create();

    $this->withToken('test-demo-api-key')
        ->deleteJson('/api/demo-data')
        ->assertOk()
        ->assertExactJson([
            'message' => 'Demo data wiped.',
            'deleted' => [
                'kaijus' => 2,
                'incidents' => 3,
            ],
        ]);

    $this->assertDatabaseCount('kaijus', 0);
    $this->assertDatabaseCount('incidents', 0);
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

test('the seed route runs the repeatable application seeders', function () {
    $this->withToken('test-demo-api-key')
        ->postJson('/api/demo-data/seed')
        ->assertOk()
        ->assertExactJson([
            'message' => 'Demo data seeded.',
            'records' => [
                'kaijus' => 12,
                'incidents' => 9,
            ],
        ]);

    $this->assertDatabaseCount('kaijus', 12);
    $this->assertDatabaseCount('incidents', 9);
});

test('seeding preserves additional records and remains repeatable', function () {
    $extraKaiju = Kaiju::factory()->create(['name' => 'Demo Visitor']);

    $this->withToken('test-demo-api-key')->postJson('/api/demo-data/seed')->assertOk();
    $this->withToken('test-demo-api-key')->postJson('/api/demo-data/seed')->assertOk();

    $this->assertDatabaseCount('kaijus', 13);
    $this->assertDatabaseCount('incidents', 9);
    $this->assertDatabaseHas('kaijus', ['id' => $extraKaiju->id]);
});

test('wiping and then seeding restores the canonical demo state', function () {
    $kaiju = Kaiju::factory()->create(['name' => 'Temporary Kaiju']);
    Incident::factory()->for($kaiju)->create();

    $this->withToken('test-demo-api-key')->deleteJson('/api/demo-data')->assertOk();
    $this->withToken('test-demo-api-key')->postJson('/api/demo-data/seed')->assertOk();

    $this->assertDatabaseCount('kaijus', 12);
    $this->assertDatabaseCount('incidents', 9);
    $this->assertDatabaseMissing('kaijus', ['name' => 'Temporary Kaiju']);
});

test('demo data mutations do not accept get requests', function (string $path) {
    $this->withToken('test-demo-api-key')
        ->getJson($path)
        ->assertMethodNotAllowed();
})->with([
    'wipe route' => ['/api/demo-data'],
    'seed route' => ['/api/demo-data/seed'],
]);
