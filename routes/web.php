<?php

use Azuriom\Plugin\ExtendedTranslation\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LanguageController::class, 'index'])->name('language');
Route::post('/locale', [LanguageController::class, 'update'])->name('language.update');
Route::get('/locale/{locale}', [LanguageController::class, 'switch'])
    ->where('locale', '[A-Za-z0-9_-]+')
    ->name('language.switch');
