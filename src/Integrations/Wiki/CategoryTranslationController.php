<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Wiki\Models\Category;
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
     * Show the form for translating a wiki category.
     */
    public function edit(Category $wikiCategory, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = WikiCategoryTranslation::query()
            ->where('wiki_category_id', $wikiCategory->id)
            ->where('locale', $locale)
            ->first();

        $existing = WikiCategoryTranslation::query()
            ->where('wiki_category_id', $wikiCategory->id)
            ->pluck('locale');

        return view('extended-translation::admin.wiki.categories.edit', [
            'category' => $wikiCategory,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a wiki category translation.
     */
    public function update(CategoryTranslationRequest $request, Category $wikiCategory, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        WikiCategoryTranslation::query()->updateOrCreate(
            [
                'wiki_category_id' => $wikiCategory->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.wiki.category.updated', $wikiCategory, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.wiki.categories.edit', [$wikiCategory, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a wiki category translation.
     */
    public function destroy(Category $wikiCategory, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        WikiCategoryTranslation::query()
            ->where('wiki_category_id', $wikiCategory->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.wiki.category.deleted', $wikiCategory, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.wiki.categories.edit', [$wikiCategory, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
