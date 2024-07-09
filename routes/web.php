<?php

use App\Livewire\Translate;
use Illuminate\Support\Facades\Route;
use LaravelLang\LocaleList\Locale;

Route::get('translate', Translate::class);
Route::get('/', Translate::class)->domain('translate.'.config('app.url'));
