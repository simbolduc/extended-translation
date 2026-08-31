<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Azuriom\Plugin\Wiki\Models\Page;
use Illuminate\Support\Collection;
use WeakMap;

class PageTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, WikiPageTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Page, array{title: string, content: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the wiki page's visible fields with the translation for the current locale.
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
                'content' => $attributes['content'] ?? '',
            ];
        }

        $original = $this->originals[$page];
        $translation = $this->forPage($page, $this->locales->current());

        foreach (['title', 'content'] as $field) {
            $value = $translation?->{$field};

            $page->setAttribute(
                $field,
                is_string($value) && $value !== '' ? $value : $original[$field]
            );
            $page->syncOriginalAttribute($field);
        }
    }

    private function forPage(Page $page, ?string $locale = null): ?WikiPageTranslation
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
     * @return Collection<int, Collection<string, WikiPageTranslation>>
     */
    public function allByPage(): Collection
    {
        return $this->translations ??= WikiPageTranslation::query()
            ->get()
            ->groupBy('wiki_page_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
