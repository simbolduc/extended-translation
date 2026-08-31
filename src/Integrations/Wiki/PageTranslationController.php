<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Wiki\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private PageTranslator $translator,
    ) {
        //
    }

    /**
     * Show the form for translating a wiki page.
     */
    public function edit(Page $wikiPage, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = WikiPageTranslation::query()
            ->where('wiki_page_id', $wikiPage->id)
            ->where('locale', $locale)
            ->first();

        $existing = WikiPageTranslation::query()
            ->where('wiki_page_id', $wikiPage->id)
            ->pluck('locale');

        return view('extended-translation::admin.wiki.pages.edit', [
            'page' => $wikiPage,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a wiki page translation.
     */
    public function update(PageTranslationRequest $request, Page $wikiPage, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        WikiPageTranslation::query()->updateOrCreate(
            [
                'wiki_page_id' => $wikiPage->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.wiki.page.updated', $wikiPage, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.wiki.pages.edit', [$wikiPage, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a wiki page translation.
     */
    public function destroy(Page $wikiPage, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        WikiPageTranslation::query()
            ->where('wiki_page_id', $wikiPage->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.wiki.page.deleted', $wikiPage, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.wiki.pages.edit', [$wikiPage, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
