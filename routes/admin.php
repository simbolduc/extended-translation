<?php

use Azuriom\Plugin\ExtendedTranslation\Core\Navbar\NavbarElementTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Core\Pages\PageTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Core\Posts\PostTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Core\Settings\SettingsController;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\Permissions;
use Azuriom\Plugin\ExtendedTranslation\Integrations\Faq\FaqIntegration;
use Azuriom\Plugin\ExtendedTranslation\Integrations\Faq\QuestionTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki\CategoryTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki\PageTranslationController as WikiPageTranslationController;
use Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki\WikiController;
use Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki\WikiIntegration;
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

if (FaqIntegration::available()) {
    Route::middleware('can:'.FaqIntegration::QUESTIONS)->group(function () {
        Route::get('/faq', [QuestionTranslationController::class, 'index'])->name('faq.index');
        Route::get('/faq/{question}/{locale}', [QuestionTranslationController::class, 'edit'])
            ->where(['question' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('faq.edit');
        Route::put('/faq/{question}/{locale}', [QuestionTranslationController::class, 'update'])
            ->where(['question' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('faq.update');
        Route::delete('/faq/{question}/{locale}', [QuestionTranslationController::class, 'destroy'])
            ->where(['question' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('faq.destroy');
    });
}

if (WikiIntegration::available()) {
    Route::middleware('can:'.WikiIntegration::WIKI)->group(function () {
        Route::get('/wiki', [WikiController::class, 'index'])->name('wiki.index');
        Route::get('/wiki/pages/{wikiPage}/{locale}', [WikiPageTranslationController::class, 'edit'])
            ->where(['wikiPage' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('wiki.pages.edit');
        Route::put('/wiki/pages/{wikiPage}/{locale}', [WikiPageTranslationController::class, 'update'])
            ->where(['wikiPage' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('wiki.pages.update');
        Route::delete('/wiki/pages/{wikiPage}/{locale}', [WikiPageTranslationController::class, 'destroy'])
            ->where(['wikiPage' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('wiki.pages.destroy');
        Route::get('/wiki/categories/{wikiCategory}/{locale}', [CategoryTranslationController::class, 'edit'])
            ->where(['wikiCategory' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('wiki.categories.edit');
        Route::put('/wiki/categories/{wikiCategory}/{locale}', [CategoryTranslationController::class, 'update'])
            ->where(['wikiCategory' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('wiki.categories.update');
        Route::delete('/wiki/categories/{wikiCategory}/{locale}', [CategoryTranslationController::class, 'destroy'])
            ->where(['wikiCategory' => '[0-9]+', 'locale' => '[A-Za-z][A-Za-z0-9_-]*'])
            ->name('wiki.categories.destroy');
    });
}
