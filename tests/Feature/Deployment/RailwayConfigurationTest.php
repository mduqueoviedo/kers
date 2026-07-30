<?php

test('Railway rebuilds and seeds the disposable demo database before deployment', function () {
    $configuration = json_decode(
        file_get_contents(base_path('railway.json')),
        associative: true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($configuration['deploy']['preDeployCommand'])
        ->toBe('php artisan migrate:fresh --seed --force --no-interaction');
});
