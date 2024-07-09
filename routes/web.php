<?php

use App\Livewire\Translate;
use Illuminate\Support\Facades\Route;

Route::domain('translate.'.config('app.url'))->group(function () {
    Route::get('/', Translate::class);
});
