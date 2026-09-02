<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Shop\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private CategoryTranslator $translator,
    ) {
        //
    }

    /**
     * Show the form for translating a shop category.
     */
    public function edit(Category $shopCategory, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = ShopCategoryTranslation::query()
            ->where('shop_category_id', $shopCategory->id)
            ->where('locale', $locale)
            ->first();

        $existing = ShopCategoryTranslation::query()
            ->where('shop_category_id', $shopCategory->id)
            ->pluck('locale');

        return view('extended-translation::admin.shop.categories.edit', [
            'category' => $shopCategory,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a shop category translation.
     */
    public function update(CategoryTranslationRequest $request, Category $shopCategory, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ShopCategoryTranslation::query()->updateOrCreate(
            [
                'shop_category_id' => $shopCategory->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.shop.category.updated', $shopCategory, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.shop.categories.edit', [$shopCategory, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a shop category translation.
     */
    public function destroy(Category $shopCategory, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ShopCategoryTranslation::query()
            ->where('shop_category_id', $shopCategory->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.shop.category.deleted', $shopCategory, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.shop.categories.edit', [$shopCategory, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
