<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Azuriom\Plugin\Shop\Models\Offer;
use Illuminate\Support\Collection;
use WeakMap;

class OfferTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, ShopOfferTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Offer, array{name: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the shop offer's visible name with the translation for the current locale.
     */
    public function apply(Offer $offer): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$offer])) {
            $this->originals[$offer] = [
                'name' => (string) ($offer->getAttributes()['name'] ?? ''),
            ];
        }

        $original = $this->originals[$offer]['name'];
        $translation = $this->forOffer($offer, $this->locales->current());
        $value = $translation?->name;

        $offer->setAttribute(
            'name',
            is_string($value) && $value !== '' ? $value : $original
        );
        $offer->syncOriginalAttribute('name');
    }

    private function forOffer(Offer $offer, ?string $locale = null): ?ShopOfferTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByOffer()->get($offer->id);

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
     * @return Collection<int, Collection<string, ShopOfferTranslation>>
     */
    public function allByOffer(): Collection
    {
        return $this->translations ??= ShopOfferTranslation::query()
            ->get()
            ->groupBy('shop_offer_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
