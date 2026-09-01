<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Models\ActionLog;
use Azuriom\Plugin\Changelog\Models\Category;
use Azuriom\Plugin\Changelog\Models\Update;
use Azuriom\Plugin\ExtendedTranslation\Core\PluginIntegration;
use Azuriom\Plugin\ExtendedTranslation\Core\RegistersAdminInjectComposer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

final class ChangelogIntegration implements PluginIntegration
{
    use RegistersAdminInjectComposer;

    public const PLUGIN_ID = 'changelog';

    public const CHANGELOG = 'extended-translation.changelog';

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
        $app->singleton(UpdateTranslator::class);
        $app->singleton(CategoryTranslator::class);
        $app->singleton(TitleTranslator::class);
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
            self::CHANGELOG => 'extended-translation::changelog.permissions.changelog',
        ];
    }

    public function adminNavPermissions(): array
    {
        return [self::CHANGELOG];
    }

    public function adminNavItems(): array
    {
        return [
            'extended-translation.admin.changelog.index' => [
                'name' => trans('extended-translation::changelog.nav'),
                'permission' => self::CHANGELOG,
            ],
        ];
    }

    protected function registerRuntime(Application $app): void
    {
        if (! self::available() || ! class_exists(Update::class) || ! class_exists(Category::class)) {
            return;
        }

        Route::model('changelogUpdate', Update::class);
        Route::model('changelogCategory', Category::class);

        Update::retrieved(function (Update $update) use ($app) {
            $app->make(UpdateTranslator::class)->apply($update);
        });

        Category::retrieved(function (Category $category) use ($app) {
            $app->make(CategoryTranslator::class)->apply($category);
        });

        View::composer(['changelog::index', 'changelog::show'], TitleViewComposer::class);

        $this->registerAdminInjectComposer(
            [
                'changelog::admin.updates.index',
                'changelog::admin.updates.edit',
                'changelog::admin.categories.edit',
            ],
            AdminChangelogComposer::class,
            self::CHANGELOG,
        );

        ActionLog::registerLogs([
            'extended-translation.changelog.update.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::changelog.logs.update_updated',
                'model' => Update::class,
            ],
            'extended-translation.changelog.update.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::changelog.logs.update_deleted',
                'model' => Update::class,
            ],
            'extended-translation.changelog.category.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::changelog.logs.category_updated',
                'model' => Category::class,
            ],
            'extended-translation.changelog.category.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::changelog.logs.category_deleted',
                'model' => Category::class,
            ],
            'extended-translation.changelog.title.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::changelog.logs.title_updated',
                'model' => ChangelogTitleTranslation::class,
            ],
            'extended-translation.changelog.title.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::changelog.logs.title_deleted',
                'model' => ChangelogTitleTranslation::class,
            ],
        ]);
    }
}
