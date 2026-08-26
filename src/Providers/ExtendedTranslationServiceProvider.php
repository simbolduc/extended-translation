<?php

namespace Azuriom\Plugin\ExtendedTranslation\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\ActionLog;
use Azuriom\Models\NavbarElement;
use Azuriom\Models\Page;
use Azuriom\Models\Post;
use Azuriom\Models\Setting;
use Azuriom\Plugin\ExtendedTranslation\Models\NavbarElementTranslation;
use Azuriom\Plugin\ExtendedTranslation\Models\PageTranslation;
use Azuriom\Plugin\ExtendedTranslation\Models\PostTranslation;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\NavbarTranslator;
use Azuriom\Plugin\ExtendedTranslation\Support\PageTranslator;
use Azuriom\Plugin\ExtendedTranslation\Support\PostTranslator;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\AdminNavbarComposer;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\AdminPagesComposer;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\AdminPostsComposer;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\TranslatePostsComposer;
use Illuminate\Support\Facades\View;

class ExtendedTranslationServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        $this->app->singleton(LocaleCatalog::class);
        $this->app->singleton(PostTranslator::class);
        $this->app->singleton(PageTranslator::class);
        $this->app->singleton(NavbarTranslator::class);
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->registerAdminNavigation();

        Setting::markAsJsonEncoded('extended-translation.locales');

        Post::resolveRelationUsing('translations', function (Post $post) {
            return $post->hasMany(PostTranslation::class, 'post_id');
        });

        NavbarElement::resolveRelationUsing('translations', function (NavbarElement $element) {
            return $element->hasMany(NavbarElementTranslation::class, 'navbar_element_id');
        });

        Page::resolveRelationUsing('translations', function (Page $page) {
            return $page->hasMany(PageTranslation::class, 'page_id');
        });

        Post::retrieved(function (Post $post) {
            $this->app->make(PostTranslator::class)->apply($post);
        });

        Page::retrieved(function (Page $page) {
            $this->app->make(PageTranslator::class)->apply($page);
        });

        View::composer(['admin.posts.index', 'admin.posts.edit'], AdminPostsComposer::class);
        View::composer(['admin.pages.index', 'admin.pages.edit'], AdminPagesComposer::class);
        View::composer(['admin.navbar-elements.index', 'admin.navbar-elements.edit'], AdminNavbarComposer::class);
        View::composer(['*', 'elements.navbar'], TranslatePostsComposer::class);

        ActionLog::registerLogs([
            'extended-translation.posts.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::admin.logs.updated',
                'model' => Post::class,
            ],
            'extended-translation.posts.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::admin.logs.deleted',
                'model' => Post::class,
            ],
            'extended-translation.navbar.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::admin.logs.navbar_updated',
                'model' => NavbarElement::class,
            ],
            'extended-translation.navbar.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::admin.logs.navbar_deleted',
                'model' => NavbarElement::class,
            ],
            'extended-translation.pages.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::admin.logs.pages_updated',
                'model' => Page::class,
            ],
            'extended-translation.pages.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::admin.logs.pages_deleted',
                'model' => Page::class,
            ],
        ]);
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function adminNavigation(): array
    {
        return [
            'extended-translation' => [
                'name' => trans('extended-translation::admin.nav.title'),
                'type' => 'dropdown',
                'icon' => 'bi bi-translate',
                'route' => 'extended-translation.admin.*',
                'permission' => ['admin.posts', 'admin.pages', 'admin.navbar'],
                'items' => [
                    'extended-translation.admin.posts.index' => [
                        'name' => trans('extended-translation::admin.nav.posts'),
                        'permission' => 'admin.posts',
                    ],
                    'extended-translation.admin.pages.index' => [
                        'name' => trans('extended-translation::admin.nav.pages'),
                        'permission' => 'admin.pages',
                    ],
                    'extended-translation.admin.navbar.index' => [
                        'name' => trans('extended-translation::admin.nav.navbar'),
                        'permission' => 'admin.navbar',
                    ],
                    'extended-translation.admin.settings' => trans('extended-translation::admin.nav.settings'),
                ],
            ],
        ];
    }
}
