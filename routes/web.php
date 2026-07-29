<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/kaijus')->name('home');

Route::livewire('kaijus/create', 'pages::kaijus.create')->name('kaijus.create');
Route::livewire('kaijus', 'pages::kaijus.index')->name('kaijus.index');
