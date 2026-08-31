<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Locale;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class LocaleCatalog
{
    public const COOKIE = 'et-locale';

    /**
     * @var Collection<string, string>|null
     */
    private ?Collection $enabledCache = null;

    /**
     * Locales that can be used to translate posts.
     *
     * @return Collection<string, string> locale code => native name
     */
    public function enabled(): Collection
    {
        return $this->enabledCache ??= $this->resolveEnabledList();
    }

    /**
     * @return Collection<string, string>
     */
    protected function resolveEnabledList(): Collection
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
        $locale ??= $this->current();
        $normalized = str_replace('-', '_', $locale);
        $hyphenated = str_replace('_', '-', $locale);
        $primary = explode('_', $normalized)[0] ?? $normalized;

        return array_values(array_unique([$locale, $normalized, $hyphenated, $primary]));
    }

    /**
     * Map a locale code (cookie, request, or app locale) to an enabled catalog code.
     */
    public function resolveEnabled(?string $locale): ?string
    {
        if (! is_string($locale) || $locale === '') {
            return null;
        }

        foreach ($this->candidates($locale) as $candidate) {
            if ($this->isEnabled($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Locale persisted for this visitor (cookie, then session).
     */
    public function persisted(?Request $request = null): ?string
    {
        $request ??= request();

        if (! $request instanceof Request) {
            return null;
        }

        $values = [$request->cookie(self::COOKIE)];

        if ($request->hasSession()) {
            $values[] = $request->session()->get(self::COOKIE);
        }

        foreach ($values as $value) {
            $resolved = $this->usablePersistedValue($value);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        return null;
    }

    public function current(): string
    {
        return $this->persisted()
            ?? $this->resolveEnabled(app()->getLocale())
            ?? $this->enabled()->keys()->first()
            ?? $this->defaultLocale();
    }

    public function shortCode(?string $locale = null): string
    {
        $locale ??= $this->current();
        $primary = preg_split('/[-_]/', $locale)[0] ?? $locale;

        return strtoupper($primary);
    }

    public function apply(string $locale): void
    {
        $normalized = str_replace('-', '_', $locale);

        app()->setLocale($normalized);
        Carbon::setLocale($normalized);
    }

    public function makeCookie(string $locale): SymfonyCookie
    {
        return Cookie::forever(self::COOKIE, $locale);
    }

    public function persist(Request $request, string $locale): SymfonyCookie
    {
        $this->apply($locale);

        $request->session()->put(self::COOKIE, $locale);

        return $this->makeCookie($locale);
    }

    /**
     * Ignore leftover encrypted cookies so a plaintext `en` / `fr` value can take over.
     */
    protected function usablePersistedValue(mixed $value): ?string
    {
        if (! is_string($value) || $value === '' || str_starts_with($value, 'eyJ')) {
            return null;
        }

        return $this->resolveEnabled($value);
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
