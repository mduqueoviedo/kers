<?php

test('Railway runs migrations and seeds only the demo user before deployment', function () {
    $configuration = json_decode(
        file_get_contents(base_path('railway.json')),
        associative: true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($configuration['deploy']['preDeployCommand'])
        ->toBe('php artisan migrate --force --no-interaction && php artisan db:seed --class=Database\\Seeders\\DemoUserSeeder --force --no-interaction')
        ->not->toContain('migrate:fresh')
        ->not->toContain('--seed');
});
