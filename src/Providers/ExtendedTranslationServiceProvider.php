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
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LanguageSelectorComposer;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\SetLocale;
use Azuriom\Plugin\ExtendedTranslation\Core\Navbar\AdminNavbarComposer;
use Azuriom\Plugin\ExtendedTranslation\Core\Navbar\NavbarTranslator;
use Azuriom\Plugin\ExtendedTranslation\Core\Navbar\TranslateNavbarComposer;
use Azuriom\Plugin\ExtendedTranslation\Core\Pages\AdminPagesComposer;
use Azuriom\Plugin\ExtendedTranslation\Core\Pages\PageTranslator;
use Azuriom\Plugin\ExtendedTranslation\Core\PluginIntegration;
use Azuriom\Plugin\ExtendedTranslation\Core\Posts\AdminPostsComposer;
use Azuriom\Plugin\ExtendedTranslation\Core\Posts\PostTranslator;
use Azuriom\Plugin\ExtendedTranslation\Core\RegistersAdminInjectComposer;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\Permissions;
use Azuriom\Plugin\ExtendedTranslation\Integrations\Faq\FaqIntegration;
use Illuminate\Support\Facades\View;

class ExtendedTranslationServiceProvider extends BasePluginServiceProvider
{
    use RegistersAdminInjectComposer;

    /**
     * Optional plugin adapters. Add a class here when integrating another plugin.
     *
     * @return list<class-string<PluginIntegration>>
     */
    protected function integrations(): array
    {
        return [
            FaqIntegration::class,
        ];
    }

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

        foreach ($this->enabledIntegrations() as $integration) {
            $integration->register($this->app);
        }
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        $this->app['router']->pushMiddlewareToGroup('web', SetLocale::class);

        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->registerRouteDescriptions();
        $this->registerAdminNavigation();

        Permission::registerPermissions(Permissions::all());

        Setting::markAsJsonEncoded('extended-translation.locales');

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
        View::composer('elements.navbar', TranslateNavbarComposer::class);
        View::composer('extended-translation::dropdown', LanguageSelectorComposer::class);

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

        foreach ($this->enabledIntegrations() as $integration) {
            Permission::registerPermissions($integration->permissions());
            $integration->boot($this->app);
        }
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
        $navPermissions = [
            Permissions::POSTS,
            Permissions::PAGES,
            Permissions::NAVBAR,
            Permissions::SETTINGS,
        ];
        $navItems = [
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
        ];

        foreach ($this->enabledIntegrations() as $integration) {
            $navPermissions = [...$navPermissions, ...$integration->adminNavPermissions()];
            $navItems = [...$navItems, ...$integration->adminNavItems()];
        }

        $navItems['extended-translation.admin.settings'] = [
            'name' => trans('extended-translation::admin.nav.settings'),
            'permission' => Permissions::SETTINGS,
        ];

        return [
            'extended-translation' => [
                'name' => trans('extended-translation::admin.nav.title'),
                'type' => 'dropdown',
                'icon' => 'bi bi-translate',
                'route' => 'extended-translation.admin.*',
                'permission' => $navPermissions,
                'items' => $navItems,
            ],
        ];
    }

    /**
     * @return list<PluginIntegration>
     */
    protected function enabledIntegrations(): array
    {
        $enabled = [];

        foreach ($this->integrations() as $class) {
            if ($class::available()) {
                $enabled[] = new $class();
            }
        }

        return $enabled;
    }
}
