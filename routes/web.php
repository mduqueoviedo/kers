<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/kaijus')->name('home');

Route::post('locale', function (Request $request) {
    $locale = $request->string('locale')->toString();
    $fallback = config('app.fallback_locale', 'en');

    $request->session()->put(
        'locale',
        in_array($locale, config('kers.locales', [$fallback]), true) ? $locale : $fallback,
    );

    return back();
})->name('locale.update');

Route::livewire('incidents/create', 'pages::incidents.create')->name('incidents.create');
Route::livewire('incidents', 'pages::incidents.index')->name('incidents.index');
Route::livewire('incidents/{incident}/edit', 'pages::incidents.edit')->name('incidents.edit');
Route::livewire('incidents/{incident}', 'pages::incidents.show')->name('incidents.show');
Route::livewire('usgs-events', 'pages::usgs.index')->name('usgs.index');
Route::livewire('kaijus/create', 'pages::kaijus.create')->name('kaijus.create');
Route::livewire('kaijus', 'pages::kaijus.index')->name('kaijus.index');
Route::livewire('kaijus/{kaiju}/edit', 'pages::kaijus.edit')->name('kaijus.edit');
Route::livewire('kaijus/{kaiju}', 'pages::kaijus.show')->name('kaijus.show');
