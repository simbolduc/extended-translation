<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TitleTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private TitleTranslator $translator,
    ) {
        //
    }

    /**
     * Show the form for translating the changelog page title.
     */
    public function edit(string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $title = (string) setting('changelog.title', 'Changelog');

        $translation = ChangelogTitleTranslation::query()
            ->where('locale', $locale)
            ->first();

        $existing = ChangelogTitleTranslation::query()->pluck('locale');

        return view('extended-translation::admin.changelog.title.edit', [
            'title' => $title,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update the changelog page title translation.
     */
    public function update(TitleTranslationRequest $request, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = ChangelogTitleTranslation::query()->updateOrCreate(
            ['locale' => $locale],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.changelog.title.updated', $translation, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.changelog.title.edit', $locale)
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove the changelog page title translation.
     */
    public function destroy(string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = ChangelogTitleTranslation::query()
            ->where('locale', $locale)
            ->first();

        if ($translation !== null) {
            ActionLog::log('extended-translation.changelog.title.deleted', $translation, [
                'locale' => $locale,
            ]);

            $translation->delete();
        }

        $this->translator->forgetCache();

        return to_route('extended-translation.admin.changelog.title.edit', $locale)
            ->with('success', trans('messages.status.success'));
    }
}
