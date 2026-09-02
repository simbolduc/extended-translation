<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Shop\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PackageTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private PackageTranslator $translator,
    ) {
        //
    }

    /**
     * Show the form for translating a shop package.
     */
    public function edit(Package $shopPackage, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = ShopPackageTranslation::query()
            ->where('shop_package_id', $shopPackage->id)
            ->where('locale', $locale)
            ->first();

        $existing = ShopPackageTranslation::query()
            ->where('shop_package_id', $shopPackage->id)
            ->pluck('locale');

        return view('extended-translation::admin.shop.packages.edit', [
            'package' => $shopPackage,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a shop package translation.
     */
    public function update(PackageTranslationRequest $request, Package $shopPackage, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ShopPackageTranslation::query()->updateOrCreate(
            [
                'shop_package_id' => $shopPackage->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.shop.package.updated', $shopPackage, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.shop.packages.edit', [$shopPackage, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a shop package translation.
     */
    public function destroy(Package $shopPackage, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ShopPackageTranslation::query()
            ->where('shop_package_id', $shopPackage->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.shop.package.deleted', $shopPackage, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.shop.packages.edit', [$shopPackage, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
