<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Azuriom\Plugin\Shop\Models\Category;
use Illuminate\Support\Collection;
use WeakMap;

class CategoryTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, ShopCategoryTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Category, array{name: string, description: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the shop category's visible fields with the translation for the current locale.
     */
    public function apply(Category $category): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$category])) {
            $attributes = $category->getAttributes();

            $this->originals[$category] = [
                'name' => (string) ($attributes['name'] ?? ''),
                'description' => (string) ($attributes['description'] ?? ''),
            ];
        }

        $original = $this->originals[$category];
        $translation = $this->forCategory($category, $this->locales->current());

        foreach (['name', 'description'] as $field) {
            $value = $translation?->{$field};

            $category->setAttribute(
                $field,
                is_string($value) && $value !== '' ? $value : $original[$field]
            );
            $category->syncOriginalAttribute($field);
        }
    }

    private function forCategory(Category $category, ?string $locale = null): ?ShopCategoryTranslation
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
     * @return Collection<int, Collection<string, ShopCategoryTranslation>>
     */
    public function allByCategory(): Collection
    {
        return $this->translations ??= ShopCategoryTranslation::query()
            ->get()
            ->groupBy('shop_category_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
