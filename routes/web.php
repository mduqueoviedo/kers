<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/kaijus')->name('home');

Route::livewire('incidents/create', 'pages::incidents.create')->name('incidents.create');
Route::livewire('incidents', 'pages::incidents.index')->name('incidents.index');
Route::livewire('kaijus/create', 'pages::kaijus.create')->name('kaijus.create');
Route::livewire('kaijus', 'pages::kaijus.index')->name('kaijus.index');
Route::livewire('kaijus/{kaiju}/edit', 'pages::kaijus.edit')->name('kaijus.edit');
Route::livewire('kaijus/{kaiju}', 'pages::kaijus.show')->name('kaijus.show');
