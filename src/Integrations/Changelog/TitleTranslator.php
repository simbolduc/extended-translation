<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Illuminate\Support\Collection;

class TitleTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<string, ChangelogTitleTranslation>|null
     */
    private ?Collection $translations = null;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        //
    }

    /**
     * Return the translated changelog page title for the current locale.
     */
    public function translate(string $original): string
    {
        if (! $this->shouldApply()) {
            return $original;
        }

        $translation = $this->forLocale($this->locales->current());
        $value = $translation?->title;

        return is_string($value) && $value !== '' ? $value : $original;
    }

    private function forLocale(?string $locale = null): ?ChangelogTitleTranslation
    {
        $byLocale = $this->allByLocale();

        foreach ($this->locales->candidates($locale) as $candidate) {
            $translation = $byLocale->get($candidate);

            if ($translation !== null) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @return Collection<string, ChangelogTitleTranslation>
     */
    public function allByLocale(): Collection
    {
        return $this->translations ??= ChangelogTitleTranslation::query()
            ->get()
            ->keyBy('locale');
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
