<?php

use Azuriom\Plugin\ExtendedTranslation\Controllers\Admin\NavbarElementTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Controllers\Admin\PageTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Controllers\Admin\PostTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Controllers\Admin\SettingsController;
use Azuriom\Plugin\ExtendedTranslation\Support\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware('can:'.Permissions::SETTINGS)->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

Route::middleware('can:'.Permissions::POSTS)->group(function () {
    Route::get('/', [PostTranslationController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post}/{locale}', [PostTranslationController::class, 'edit'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('posts.edit');
    Route::put('/posts/{post}/{locale}', [PostTranslationController::class, 'update'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('posts.update');
    Route::delete('/posts/{post}/{locale}', [PostTranslationController::class, 'destroy'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('posts.destroy');
});

Route::middleware('can:'.Permissions::PAGES)->group(function () {
    Route::get('/pages', [PageTranslationController::class, 'index'])->name('pages.index');
    Route::get('/pages/{page}/{locale}', [PageTranslationController::class, 'edit'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('pages.edit');
    Route::put('/pages/{page}/{locale}', [PageTranslationController::class, 'update'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('pages.update');
    Route::delete('/pages/{page}/{locale}', [PageTranslationController::class, 'destroy'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('pages.destroy');
});

Route::middleware('can:'.Permissions::NAVBAR)->group(function () {
    Route::get('/navbar', [NavbarElementTranslationController::class, 'index'])->name('navbar.index');
    Route::get('/navbar/{navbarElement}/{locale}', [NavbarElementTranslationController::class, 'edit'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('navbar.edit');
    Route::put('/navbar/{navbarElement}/{locale}', [NavbarElementTranslationController::class, 'update'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('navbar.update');
    Route::delete('/navbar/{navbarElement}/{locale}', [NavbarElementTranslationController::class, 'destroy'])
        ->where('locale', '[A-Za-z][A-Za-z0-9_-]*')
        ->name('navbar.destroy');
});
