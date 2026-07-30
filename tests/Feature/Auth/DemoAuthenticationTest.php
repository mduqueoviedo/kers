<?php

use App\Models\Incident;
use App\Models\Kaiju;
use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

test('the deterministic demo user is recreated from configuration', function () {
    config()->set('kers.demo_user', [
        'name' => 'Demo Controller',
        'email' => 'controller@kers.test',
        'password' => 'configured-demo-password',
    ]);

    $this->seed(DemoUserSeeder::class);
    $this->seed(DemoUserSeeder::class);

    $user = User::query()->sole();

    expect($user->name)->toBe('Demo Controller')
        ->and($user->email)->toBe('controller@kers.test')
        ->and(Hash::check('configured-demo-password', $user->password))->toBeTrue();
});

test('the configured demo user can log in and credentials are shown', function () {
    $this->seed(DemoUserSeeder::class);
    $demoUser = config('kers.demo_user');

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Demo credentials')
        ->assertSee($demoUser['email'])
        ->assertSee($demoUser['password'])
        ->assertDontSee('Sign up');

    $this->post(route('login.store'), [
        'email' => $demoUser['email'],
        'password' => $demoUser['password'],
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('kaijus.index', absolute: false));

    $this->assertAuthenticatedAs(User::query()->sole());
});

test('login rejects an invalid demo password and logout ends the session', function () {
    $this->seed(DemoUserSeeder::class);
    $demoUser = config('kers.demo_user');

    $this->post(route('login.store'), [
        'email' => $demoUser['email'],
        'password' => 'incorrect-password',
    ])->assertSessionHasErrorsIn('email');

    $this->assertGuest();

    $this->actingAs(User::query()->sole())
        ->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});

test('registration, password recovery, and email verification routes are unavailable', function () {
    expect(Route::has('register'))->toBeFalse()
        ->and(Route::has('password.request'))->toBeFalse()
        ->and(Route::has('verification.notice'))->toBeFalse();

    $this->get('/register')->assertNotFound();
    $this->get('/forgot-password')->assertNotFound();
    $this->get('/email/verify')->assertNotFound();
});

test('guests retain read access but mutation routes redirect to login and controls stay hidden', function () {
    $kaiju = Kaiju::factory()->create();
    $incident = Incident::factory()->for($kaiju)->create();
    Http::fake(['*' => Http::response(['features' => []])]);

    $this->get(route('kaijus.index'))
        ->assertOk()
        ->assertDontSee('Register kaiju')
        ->assertDontSee('Record incident');
    $this->get(route('kaijus.show', $kaiju))->assertOk()->assertDontSeeHtml('wire:click="requestDeletion"');
    $this->get(route('incidents.index'))->assertOk()->assertDontSee('Record incident');
    $this->get(route('incidents.show', $incident))->assertOk()->assertDontSeeHtml('wire:click="requestDeletion"');
    $this->get('/usgs-events')->assertOk()->assertDontSee('data-test="import-incident"', false);

    $this->get(route('kaijus.create'))->assertRedirect(route('login'));
    $this->get(route('kaijus.edit', $kaiju))->assertRedirect(route('login'));
    $this->get(route('incidents.create'))->assertRedirect(route('login'));
    $this->get(route('incidents.edit', $incident))->assertRedirect(route('login'));
});

test('authenticated users can access mutation routes and controls', function () {
    $user = User::factory()->create();
    $kaiju = Kaiju::factory()->create();
    $incident = Incident::factory()->for($kaiju)->create();

    $this->actingAs($user)->get('/kaijus')->assertOk()->assertSee('Register kaiju');
    $this->actingAs($user)->get(route('kaijus.show', $kaiju))->assertOk()->assertSee('Edit kaiju')->assertSee('Delete kaiju');
    $this->actingAs($user)->get('/incidents')->assertOk()->assertSee('Record incident');
    $this->actingAs($user)->get(route('incidents.show', $incident))->assertOk()->assertSeeHtml('wire:click="requestDeletion"');
    $this->actingAs($user)->get('/kaijus/create')->assertOk();
    $this->actingAs($user)->get(route('kaijus.edit', $kaiju))->assertOk();
    $this->actingAs($user)->get('/incidents/create')->assertOk();
    $this->actingAs($user)->get(route('incidents.edit', $incident))->assertOk();
});

test('guests cannot invoke Livewire mutation actions directly', function () {
    $kaiju = Kaiju::factory()->create();
    $incident = Incident::factory()->for($kaiju)->create();
    Http::fake(['*' => Http::response(['features' => []])]);

    Livewire::test('pages::kaijus.create')->call('save')->assertForbidden();
    Livewire::test('pages::kaijus.edit', ['kaiju' => $kaiju])->call('save')->assertForbidden();
    Livewire::test('pages::kaijus.show', ['kaiju' => $kaiju])->call('requestDeletion')->assertForbidden();
    Livewire::test('pages::incidents.create')->call('save')->assertForbidden();
    Livewire::test('pages::incidents.edit', ['incident' => $incident])->call('save')->assertForbidden();
    Livewire::test('pages::incidents.show', ['incident' => $incident])->call('requestDeletion')->assertForbidden();
    Livewire::test('pages::usgs.index')->call('importIncident')->assertForbidden();
});
