<?php

function translationKeys(array $translations, string $prefix = ''): array
{
    return collect($translations)
        ->flatMap(fn ($value, $key) => is_array($value)
            ? translationKeys($value, $prefix.$key.'.')
            : [$prefix.$key])
        ->sort()
        ->values()
        ->all();
}

test('English and Spanish translation resources have matching keys', function () {
    $englishFiles = collect(glob(lang_path('en/*.php')))
        ->mapWithKeys(fn (string $path) => [basename($path) => translationKeys(require $path)])
        ->all();
    $spanishFiles = collect(glob(lang_path('es/*.php')))
        ->mapWithKeys(fn (string $path) => [basename($path) => translationKeys(require $path)])
        ->all();

    expect($spanishFiles)->toBe($englishFiles);
});
