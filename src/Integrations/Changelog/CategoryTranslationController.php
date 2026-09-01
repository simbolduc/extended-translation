<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\Changelog\Models\Category;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
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
     * Show the form for translating a changelog category.
     */
    public function edit(Category $changelogCategory, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = ChangelogCategoryTranslation::query()
            ->where('changelog_category_id', $changelogCategory->id)
            ->where('locale', $locale)
            ->first();

        $existing = ChangelogCategoryTranslation::query()
            ->where('changelog_category_id', $changelogCategory->id)
            ->pluck('locale');

        return view('extended-translation::admin.changelog.categories.edit', [
            'category' => $changelogCategory,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a changelog category translation.
     */
    public function update(CategoryTranslationRequest $request, Category $changelogCategory, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ChangelogCategoryTranslation::query()->updateOrCreate(
            [
                'changelog_category_id' => $changelogCategory->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.changelog.category.updated', $changelogCategory, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.changelog.categories.edit', [$changelogCategory, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a changelog category translation.
     */
    public function destroy(Category $changelogCategory, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ChangelogCategoryTranslation::query()
            ->where('changelog_category_id', $changelogCategory->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.changelog.category.deleted', $changelogCategory, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.changelog.categories.edit', [$changelogCategory, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
