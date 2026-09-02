<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\PluginIntegration;
use Azuriom\Plugin\ExtendedTranslation\Core\RegistersAdminInjectComposer;
use Azuriom\Plugin\Shop\Models\Category;
use Azuriom\Plugin\Shop\Models\Offer;
use Azuriom\Plugin\Shop\Models\Package;
use Azuriom\Plugin\Shop\Models\Variable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;

final class ShopIntegration implements PluginIntegration
{
    use RegistersAdminInjectComposer;

    public const PLUGIN_ID = 'shop';

    public const SHOP = 'extended-translation.shop';

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
        $app->singleton(CategoryTranslator::class);
        $app->singleton(PackageTranslator::class);
        $app->singleton(OfferTranslator::class);
        $app->singleton(VariableTranslator::class);
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
            self::SHOP => 'extended-translation::shop.permissions.shop',
        ];
    }

    public function adminNavPermissions(): array
    {
        return [self::SHOP];
    }

    public function adminNavItems(): array
    {
        return [
            'extended-translation.admin.shop.index' => [
                'name' => trans('extended-translation::shop.nav'),
                'permission' => self::SHOP,
            ],
        ];
    }

    protected function registerRuntime(Application $app): void
    {
        if (
            ! self::available()
            || ! class_exists(Category::class)
            || ! class_exists(Package::class)
            || ! class_exists(Offer::class)
            || ! class_exists(Variable::class)
        ) {
            return;
        }

        Route::model('shopCategory', Category::class);
        Route::model('shopPackage', Package::class);
        Route::model('shopOffer', Offer::class);
        Route::model('shopVariable', Variable::class);

        Category::retrieved(function (Category $category) use ($app) {
            $app->make(CategoryTranslator::class)->apply($category);
        });

        Package::retrieved(function (Package $package) use ($app) {
            $app->make(PackageTranslator::class)->apply($package);
        });

        Offer::retrieved(function (Offer $offer) use ($app) {
            $app->make(OfferTranslator::class)->apply($offer);
        });

        Variable::retrieved(function (Variable $variable) use ($app) {
            $app->make(VariableTranslator::class)->apply($variable);
        });

        $this->registerAdminInjectComposer(
            [
                'shop::admin.packages.index',
                'shop::admin.packages.edit',
                'shop::admin.categories.edit',
                'shop::admin.offers.index',
                'shop::admin.offers.edit',
                'shop::admin.variables.index',
                'shop::admin.variables.edit',
            ],
            AdminShopComposer::class,
            self::SHOP,
        );

        ActionLog::registerLogs([
            'extended-translation.shop.package.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::shop.logs.package_updated',
                'model' => Package::class,
            ],
            'extended-translation.shop.package.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::shop.logs.package_deleted',
                'model' => Package::class,
            ],
            'extended-translation.shop.category.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::shop.logs.category_updated',
                'model' => Category::class,
            ],
            'extended-translation.shop.category.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::shop.logs.category_deleted',
                'model' => Category::class,
            ],
            'extended-translation.shop.offer.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::shop.logs.offer_updated',
                'model' => Offer::class,
            ],
            'extended-translation.shop.offer.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::shop.logs.offer_deleted',
                'model' => Offer::class,
            ],
            'extended-translation.shop.variable.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::shop.logs.variable_updated',
                'model' => Variable::class,
            ],
            'extended-translation.shop.variable.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::shop.logs.variable_deleted',
                'model' => Variable::class,
            ],
        ]);
    }
}
