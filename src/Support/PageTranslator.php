<?php

namespace Azuriom\Plugin\ExtendedTranslation\Support;

use Azuriom\Models\Page;
use Azuriom\Plugin\ExtendedTranslation\Models\PageTranslation;
use Illuminate\Support\Collection;
use WeakMap;

class PageTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, PageTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Page, array{title: string, description: string, content: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the page's visible fields with the translation for the current locale.
     */
    public function apply(Page $page): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$page])) {
            $attributes = $page->getAttributes();

            $this->originals[$page] = [
                'title' => $attributes['title'] ?? '',
                'description' => $attributes['description'] ?? '',
                'content' => $attributes['content'] ?? '',
            ];
        }

        $original = $this->originals[$page];
        $translation = $this->forPage($page, $this->locales->current());

        foreach (['title', 'description', 'content'] as $field) {
            $value = $translation?->{$field};

            $page->setAttribute(
                $field,
                is_string($value) && $value !== '' ? $value : $original[$field]
            );
            $page->syncOriginalAttribute($field);
        }
    }

    public function forPage(Page $page, ?string $locale = null): ?PageTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByPage()->get($page->id);

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
     * @return Collection<int, Collection<string, PageTranslation>>
     */
    public function allByPage(): Collection
    {
        return $this->translations ??= PageTranslation::query()
            ->get()
            ->groupBy('page_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
