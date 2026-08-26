<?php

namespace Azuriom\Plugin\ExtendedTranslation\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class LocaleCatalog
{
    /**
     * Locales that can be used to translate posts.
     *
     * @return Collection<string, string> locale code => native name
     */
    public function enabled(): Collection
    {
        $selected = $this->selectedLocales();

        if ($selected->isNotEmpty()) {
            return $this->named($selected)->intersectByKeys($this->installed());
        }

        return $this->defaults();
    }

    /**
     * All locales shipped with Azuriom.
     *
     * @return Collection<string, string>
     */
    public function installed(): Collection
    {
        return $this->named(
            collect(File::directories(app()->langPath()))
                ->map(fn (string $path) => basename($path))
        );
    }

    public function defaultLocale(): string
    {
        return (string) (setting('locale') ?: config('app.locale', 'en'));
    }

    public function isEnabled(string $locale): bool
    {
        return $this->enabled()->has($locale);
    }

    public function name(string $locale): string
    {
        return $this->installed()->get($locale, $locale);
    }

    /**
     * Locale codes that should match the current application locale.
     *
     * @return list<string>
     */
    public function candidates(?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $normalized = str_replace('-', '_', $locale);
        $primary = explode('_', $normalized)[0] ?? $normalized;

        return array_values(array_unique([$locale, $normalized, $primary]));
    }

    /**
     * Prefer a missing non-default locale when linking to a translation form.
     *
     * @param  Collection<int, string>  $existing
     */
    public function firstTargetLocale(Collection $existing): string
    {
        $default = $this->defaultLocale();

        return $this->enabled()->keys()->first(
            fn (string $code) => $code !== $default && ! $existing->contains($code)
        ) ?? $this->enabled()->keys()->first(
            fn (string $code) => $code !== $default
        ) ?? $this->enabled()->keys()->first() ?? $default;
    }

    /**
     * @return Collection<string, string>
     */
    protected function defaults(): Collection
    {
        $slLanguage = 'Azuriom\\Plugin\\SlLanguage\\LocaleManager';

        if (plugins()->isEnabled('sl-language') && class_exists($slLanguage) && app()->bound($slLanguage)) {
            /** @var Collection<string, string> $available */
            $available = app($slLanguage)->available();

            if ($available->isNotEmpty()) {
                return $available;
            }
        }

        $codes = collect([$this->defaultLocale(), 'en', 'fr'])->unique();

        return $this->named($codes)->intersectByKeys($this->installed());
    }

    /**
     * @return Collection<int, string>
     */
    protected function selectedLocales(): Collection
    {
        $selected = setting('extended-translation.locales');

        if (is_string($selected) && $selected !== '') {
            $decoded = json_decode($selected, true);
            $selected = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($selected)) {
            return collect();
        }

        return collect($selected)->filter(fn ($locale) => is_string($locale) && $locale !== '');
    }

    /**
     * @param  Collection<int, string>  $codes
     * @return Collection<string, string>
     */
    protected function named(Collection $codes): Collection
    {
        return $codes
            ->filter()
            ->unique()
            ->mapWithKeys(function (string $locale) {
                $name = trans('messages.lang', [], $locale);

                return [$locale => is_string($name) && $name !== 'messages.lang' ? $name : $locale];
            });
    }
}
