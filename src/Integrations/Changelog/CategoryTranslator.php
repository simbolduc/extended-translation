<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Plugin\Changelog\Models\Category;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Illuminate\Support\Collection;
use WeakMap;

class CategoryTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, ChangelogCategoryTranslation>>|null
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
     * Replace the changelog category's visible name with the translation for the current locale.
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

    private function forCategory(Category $category, ?string $locale = null): ?ChangelogCategoryTranslation
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
     * @return Collection<int, Collection<string, ChangelogCategoryTranslation>>
     */
    public function allByCategory(): Collection
    {
        return $this->translations ??= ChangelogCategoryTranslation::query()
            ->get()
            ->groupBy('changelog_category_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
