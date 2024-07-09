<?php

use App\Livewire\Translate;
use Illuminate\Support\Facades\Route;

Route::get('translate', Translate::class);
Route::get('/', Translate::class)->domain('translate.'.config('app.url'));
Route::get('test', function () {
    dd((new LaravelLang\Translator\Services\Translate())->viaGoogle('Привет', 'uz', 'ru'));
});
