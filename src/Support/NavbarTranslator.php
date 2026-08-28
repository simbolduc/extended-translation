<?php

namespace Azuriom\Plugin\ExtendedTranslation\Support;

use Azuriom\Models\NavbarElement;
use Azuriom\Plugin\ExtendedTranslation\Models\NavbarElementTranslation;
use Illuminate\Support\Collection;
use WeakMap;

class NavbarTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, NavbarElementTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<NavbarElement, array{name: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the element's visible name with the translation for the current locale.
     */
    public function apply(NavbarElement $element): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$element])) {
            $this->originals[$element] = [
                'name' => (string) ($element->getRawOriginal('name') ?? ''),
            ];
        }

        $original = $this->originals[$element]['name'];
        $translation = $this->forElement($element, $this->locales->current());
        $value = $translation?->name;

        $element->setAttribute(
            'name',
            is_string($value) && $value !== '' ? $value : $original
        );
        $element->syncOriginalAttribute('name');
    }

    public function forElement(NavbarElement $element, ?string $locale = null): ?NavbarElementTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByElement()->get($element->id);

        if ($byLocale === null) {
            return null;
        }

        foreach ($candidates as $candidate) {
            $translation = $byLocale->get($candidate);

            if ($translation !== null) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Collection<string, NavbarElementTranslation>>
     */
    public function allByElement(): Collection
    {
        return $this->translations ??= NavbarElementTranslation::query()
            ->get()
            ->groupBy('navbar_element_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
