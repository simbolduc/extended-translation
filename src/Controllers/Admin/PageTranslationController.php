<?php

namespace Azuriom\Plugin\ExtendedTranslation\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Models\Page;
use Azuriom\Plugin\ExtendedTranslation\Http\Requests\Admin\PageTranslationRequest;
use Azuriom\Plugin\ExtendedTranslation\Models\PageTranslation;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\PageTranslator;
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
     * Display the pages that can be translated.
     */
    public function index(): View
    {
        $pages = Page::query()
            ->latest()
            ->paginate();

        return view('extended-translation::admin.pages.index', [
            'pages' => $pages,
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translations' => $this->translator->allByPage(),
        ]);
    }

    /**
     * Show the form for translating a page.
     */
    public function edit(Page $page, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = PageTranslation::query()
            ->where('page_id', $page->id)
            ->where('locale', $locale)
            ->first();

        $existing = PageTranslation::query()
            ->where('page_id', $page->id)
            ->pluck('locale');

        return view('extended-translation::admin.pages.edit', [
            'page' => $page,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a page translation.
     */
    public function update(PageTranslationRequest $request, Page $page, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        PageTranslation::query()->updateOrCreate(
            [
                'page_id' => $page->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.pages.updated', $page, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.pages.edit', [$page, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a page translation.
     */
    public function destroy(Page $page, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        PageTranslation::query()
            ->where('page_id', $page->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.pages.deleted', $page, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.pages.edit', [$page, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
