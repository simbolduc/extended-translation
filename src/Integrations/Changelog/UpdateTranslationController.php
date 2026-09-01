<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\Changelog\Models\Update;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UpdateTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private UpdateTranslator $translator,
    ) {
        //
    }

    /**
     * Show the form for translating a changelog update.
     */
    public function edit(Update $changelogUpdate, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = ChangelogUpdateTranslation::query()
            ->where('changelog_update_id', $changelogUpdate->id)
            ->where('locale', $locale)
            ->first();

        $existing = ChangelogUpdateTranslation::query()
            ->where('changelog_update_id', $changelogUpdate->id)
            ->pluck('locale');

        return view('extended-translation::admin.changelog.updates.edit', [
            'update' => $changelogUpdate,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a changelog update translation.
     */
    public function update(UpdateTranslationRequest $request, Update $changelogUpdate, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ChangelogUpdateTranslation::query()->updateOrCreate(
            [
                'changelog_update_id' => $changelogUpdate->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.changelog.update.updated', $changelogUpdate, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.changelog.updates.edit', [$changelogUpdate, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a changelog update translation.
     */
    public function destroy(Update $changelogUpdate, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ChangelogUpdateTranslation::query()
            ->where('changelog_update_id', $changelogUpdate->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.changelog.update.deleted', $changelogUpdate, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.changelog.updates.edit', [$changelogUpdate, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
