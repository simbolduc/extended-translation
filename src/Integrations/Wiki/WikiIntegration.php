<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\PluginIntegration;
use Azuriom\Plugin\ExtendedTranslation\Core\RegistersAdminInjectComposer;
use Azuriom\Plugin\Wiki\Models\Category;
use Azuriom\Plugin\Wiki\Models\Page;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;

final class WikiIntegration implements PluginIntegration
{
    use RegistersAdminInjectComposer;

    public const PLUGIN_ID = 'wiki';

    public const WIKI = 'extended-translation.wiki';

    public static function pluginId(): string
    {
        return self::PLUGIN_ID;
    }

    public static function available(): bool
    {
        return plugins()->isEnabled(self::PLUGIN_ID);
    }

    public function register(Application $app): void
    {
        $app->singleton(PageTranslator::class);
        $app->singleton(CategoryTranslator::class);
    }

    public function boot(Application $app): void
    {
        $app->booted(function () use ($app) {
            $this->registerRuntime($app);
        });
    }

    public function permissions(): array
    {
        return [
            self::WIKI => 'extended-translation::wiki.permissions.wiki',
        ];
    }

    public function adminNavPermissions(): array
    {
        return [self::WIKI];
    }

    public function adminNavItems(): array
    {
        return [
            'extended-translation.admin.wiki.index' => [
                'name' => trans('extended-translation::wiki.nav'),
                'permission' => self::WIKI,
            ],
        ];
    }

    protected function registerRuntime(Application $app): void
    {
        if (! self::available() || ! class_exists(Page::class) || ! class_exists(Category::class)) {
            return;
        }

        Route::model('wikiPage', Page::class);
        Route::model('wikiCategory', Category::class);

        Page::retrieved(function (Page $page) use ($app) {
            $app->make(PageTranslator::class)->apply($page);
        });

        Category::retrieved(function (Category $category) use ($app) {
            $app->make(CategoryTranslator::class)->apply($category);
        });

        $this->registerAdminInjectComposer(
            [
                'wiki::admin.pages.index',
                'wiki::admin.pages.edit',
                'wiki::admin.categories.edit',
            ],
            AdminWikiComposer::class,
            self::WIKI,
        );

        ActionLog::registerLogs([
            'extended-translation.wiki.page.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::wiki.logs.page_updated',
                'model' => Page::class,
            ],
            'extended-translation.wiki.page.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::wiki.logs.page_deleted',
                'model' => Page::class,
            ],
            'extended-translation.wiki.category.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::wiki.logs.category_updated',
                'model' => Category::class,
            ],
            'extended-translation.wiki.category.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::wiki.logs.category_deleted',
                'model' => Category::class,
            ],
        ]);
    }
}
