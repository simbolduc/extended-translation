<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Shop\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OfferTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private OfferTranslator $translator,
    ) {
        //
    }

    /**
     * Show the form for translating a shop offer.
     */
    public function edit(Offer $shopOffer, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = ShopOfferTranslation::query()
            ->where('shop_offer_id', $shopOffer->id)
            ->where('locale', $locale)
            ->first();

        $existing = ShopOfferTranslation::query()
            ->where('shop_offer_id', $shopOffer->id)
            ->pluck('locale');

        return view('extended-translation::admin.shop.offers.edit', [
            'offer' => $shopOffer,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a shop offer translation.
     */
    public function update(OfferTranslationRequest $request, Offer $shopOffer, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ShopOfferTranslation::query()->updateOrCreate(
            [
                'shop_offer_id' => $shopOffer->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.shop.offer.updated', $shopOffer, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.shop.offers.edit', [$shopOffer, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a shop offer translation.
     */
    public function destroy(Offer $shopOffer, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ShopOfferTranslation::query()
            ->where('shop_offer_id', $shopOffer->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.shop.offer.deleted', $shopOffer, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.shop.offers.edit', [$shopOffer, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
