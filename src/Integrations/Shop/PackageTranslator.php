<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Azuriom\Plugin\Shop\Models\Package;
use Illuminate\Support\Collection;
use WeakMap;

class PackageTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, ShopPackageTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Package, array{name: string, short_description: string, description: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the shop package's visible fields with the translation for the current locale.
     */
    public function apply(Package $package): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$package])) {
            $attributes = $package->getAttributes();

            $this->originals[$package] = [
                'name' => (string) ($attributes['name'] ?? ''),
                'short_description' => (string) ($attributes['short_description'] ?? ''),
                'description' => (string) ($attributes['description'] ?? ''),
            ];
        }

        $original = $this->originals[$package];
        $translation = $this->forPackage($package, $this->locales->current());

        foreach (['name', 'short_description', 'description'] as $field) {
            $value = $translation?->{$field};

            $package->setAttribute(
                $field,
                is_string($value) && $value !== '' ? $value : $original[$field]
            );
            $package->syncOriginalAttribute($field);
        }
    }

    private function forPackage(Package $package, ?string $locale = null): ?ShopPackageTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByPackage()->get($package->id);

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
     * @return Collection<int, Collection<string, ShopPackageTranslation>>
     */
    public function allByPackage(): Collection
    {
        return $this->translations ??= ShopPackageTranslation::query()
            ->get()
            ->groupBy('shop_package_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
