<?php

use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', ['en', 'ps', 'fa'])
    ->name('locale.switch');
