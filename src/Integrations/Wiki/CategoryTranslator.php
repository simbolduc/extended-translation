<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Azuriom\Plugin\Wiki\Models\Category;
use Illuminate\Support\Collection;
use WeakMap;

class CategoryTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, WikiCategoryTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Category, array{name: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the wiki category's visible name with the translation for the current locale.
     */
    public function apply(Category $category): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$category])) {
            $this->originals[$category] = [
                'name' => (string) ($category->getAttributes()['name'] ?? ''),
            ];
        }

        $original = $this->originals[$category]['name'];
        $translation = $this->forCategory($category, $this->locales->current());
        $value = $translation?->name;

        $category->setAttribute(
            'name',
            is_string($value) && $value !== '' ? $value : $original
        );
        $category->syncOriginalAttribute('name');
    }

    private function forCategory(Category $category, ?string $locale = null): ?WikiCategoryTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByCategory()->get($category->id);

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
     * @return Collection<int, Collection<string, WikiCategoryTranslation>>
     */
    public function allByCategory(): Collection
    {
        return $this->translations ??= WikiCategoryTranslation::query()
            ->get()
            ->groupBy('wiki_category_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
