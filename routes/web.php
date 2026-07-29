<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/kaijus')->name('home');

Route::livewire('kaijus', 'pages::kaijus.index')->name('kaijus.index');
