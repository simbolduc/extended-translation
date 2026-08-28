<?php

namespace Azuriom\Plugin\ExtendedTranslation\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Http\Middleware\EncryptCookies;
use Azuriom\Models\ActionLog;
use Azuriom\Models\NavbarElement;
use Azuriom\Models\Page;
use Azuriom\Models\Permission;
use Azuriom\Models\Post;
use Azuriom\Models\Setting;
use Azuriom\Plugin\ExtendedTranslation\Middleware\SetLocale;
use Azuriom\Plugin\ExtendedTranslation\Models\NavbarElementTranslation;
use Azuriom\Plugin\ExtendedTranslation\Models\PageTranslation;
use Azuriom\Plugin\ExtendedTranslation\Models\PostTranslation;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\NavbarTranslator;
use Azuriom\Plugin\ExtendedTranslation\Support\PageTranslator;
use Azuriom\Plugin\ExtendedTranslation\Support\Permissions;
use Azuriom\Plugin\ExtendedTranslation\Support\PostTranslator;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\AdminNavbarComposer;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\AdminPagesComposer;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\AdminPostsComposer;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\LocaleComposer;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\NavbarComposer;
use Azuriom\Plugin\ExtendedTranslation\View\Composers\TranslatePostsComposer;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as IlluminateView;

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

        EncryptCookies::except(LocaleCatalog::COOKIE);

        $this->app->afterResolving(EncryptCookies::class, function (EncryptCookies $cookies) {
            $cookies->disableFor(LocaleCatalog::COOKIE);
        });

        if ($this->app->runningInConsole()) {
            return;
        }

        $this->app->make(Kernel::class)
            ->appendMiddlewareToGroup('web', SetLocale::class);
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        $this->app['router']->pushMiddlewareToGroup('web', SetLocale::class);

        View::composer('*', LocaleComposer::class);
        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->registerRouteDescriptions();
        $this->registerAdminNavigation();

        Permission::registerPermissions(Permissions::all());

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

        $this->registerAdminInjectComposer(
            ['admin.posts.index', 'admin.posts.edit'],
            AdminPostsComposer::class,
            Permissions::POSTS,
        );
        $this->registerAdminInjectComposer(
            ['admin.pages.index', 'admin.pages.edit'],
            AdminPagesComposer::class,
            Permissions::PAGES,
        );
        $this->registerAdminInjectComposer(
            ['admin.navbar-elements.index', 'admin.navbar-elements.edit'],
            AdminNavbarComposer::class,
            Permissions::NAVBAR,
        );
        View::composer(['*', 'elements.navbar'], TranslatePostsComposer::class);
        View::composer([
            'elements.navbar',
            'extended-translation::selector',
            'extended-translation::dropdown',
        ], NavbarComposer::class);

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
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array<string, string>
     */
    protected function routeDescriptions(): array
    {
        return [
            'extended-translation.language' => trans('extended-translation::messages.title'),
        ];
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
                'permission' => [
                    Permissions::POSTS,
                    Permissions::PAGES,
                    Permissions::NAVBAR,
                    Permissions::SETTINGS,
                ],
                'items' => [
                    'extended-translation.admin.posts.index' => [
                        'name' => trans('extended-translation::admin.nav.posts'),
                        'permission' => Permissions::POSTS,
                    ],
                    'extended-translation.admin.pages.index' => [
                        'name' => trans('extended-translation::admin.nav.pages'),
                        'permission' => Permissions::PAGES,
                    ],
                    'extended-translation.admin.navbar.index' => [
                        'name' => trans('extended-translation::admin.nav.navbar'),
                        'permission' => Permissions::NAVBAR,
                    ],
                    'extended-translation.admin.settings' => [
                        'name' => trans('extended-translation::admin.nav.settings'),
                        'permission' => Permissions::SETTINGS,
                    ],
                ],
            ],
        ];
    }

    /**
     * Register a core-admin inject composer only when the user can translate that resource.
     *
     * @param  array<int, string>  $views
     * @param  class-string  $composer
     */
    protected function registerAdminInjectComposer(array $views, string $composer, string $permission): void
    {
        View::composer($views, function (IlluminateView $view) use ($composer, $permission): void {
            if (! Gate::allows($permission)) {
                return;
            }

            app($composer)->compose($view);
        });
    }
}
