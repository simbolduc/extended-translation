<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\PluginOptions;
use Azuriom\Plugin\Shop\Models\Category;
use Azuriom\Plugin\Shop\Models\Offer;
use Azuriom\Plugin\Shop\Models\Package;
use Azuriom\Plugin\Shop\Models\Variable;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminShopComposer
{
    public function __construct(
        private LocaleCatalog $locales,
        private CategoryTranslator $categories,
        private PackageTranslator $packages,
        private OfferTranslator $offers,
        private VariableTranslator $variables,
    ) {
        //
    }

    public function compose(View $view): void
    {
        if (! PluginOptions::injectCoreAdmin()) {
            return;
        }

        $data = $view->getData();
        $payload = [
            'label' => trans('extended-translation::admin.actions.translate'),
            'indexUrl' => route('extended-translation.admin.shop.index'),
        ];

        if (isset($data['package']) && $data['package'] instanceof Package) {
            $payload['editUrl'] = $this->packageEditUrl($data['package']);
        }

        if (isset($data['category']) && $data['category'] instanceof Category) {
            $payload['editUrl'] = $this->categoryEditUrl($data['category']);
        }

        if (isset($data['offer']) && $data['offer'] instanceof Offer) {
            $payload['editUrl'] = $this->offerEditUrl($data['offer']);
        }

        if (isset($data['variable']) && $data['variable'] instanceof Variable) {
            $payload['editUrl'] = $this->variableEditUrl($data['variable']);
        }

        if (isset($data['categories'])) {
            $categories = $this->flattenCategories(Collection::make($data['categories']));

            $payload['shopCategories'] = $categories
                ->filter(fn ($category) => $category instanceof Category)
                ->mapWithKeys(fn (Category $category) => [$category->id => $this->categoryEditUrl($category)])
                ->all();

            $payload['shopPackages'] = $this->flattenPackages(Collection::make($data['categories']))
                ->filter(fn ($package) => $package instanceof Package)
                ->mapWithKeys(fn (Package $package) => [$package->id => $this->packageEditUrl($package)])
                ->all();
        }

        if (isset($data['offers'])) {
            $payload['shopOffers'] = Collection::make($data['offers'])
                ->filter(fn ($offer) => $offer instanceof Offer)
                ->mapWithKeys(fn (Offer $offer) => [$offer->id => $this->offerEditUrl($offer)])
                ->all();
        }

        if (isset($data['variables']) && ! (isset($data['package']) && $data['package'] instanceof Package)) {
            $payload['shopVariables'] = Collection::make($data['variables'])
                ->filter(fn ($variable) => $variable instanceof Variable)
                ->mapWithKeys(fn (Variable $variable) => [$variable->id => $this->variableEditUrl($variable)])
                ->all();
        }

        $view->getFactory()->startPush(
            'footer-scripts',
            view('extended-translation::admin.shop.inject', ['payload' => $payload])->render()
        );
    }

    protected function packageEditUrl(Package $package): string
    {
        $existing = $this->packages->allByPackage()->get($package->id)?->keys() ?? collect();

        return route('extended-translation.admin.shop.packages.edit', [
            $package,
            $this->locales->firstTargetLocale($existing),
        ]);
    }

    protected function categoryEditUrl(Category $category): string
    {
        $existing = $this->categories->allByCategory()->get($category->id)?->keys() ?? collect();

        return route('extended-translation.admin.shop.categories.edit', [
            $category,
            $this->locales->firstTargetLocale($existing),
        ]);
    }

    protected function offerEditUrl(Offer $offer): string
    {
        $existing = $this->offers->allByOffer()->get($offer->id)?->keys() ?? collect();

        return route('extended-translation.admin.shop.offers.edit', [
            $offer,
            $this->locales->firstTargetLocale($existing),
        ]);
    }

    protected function variableEditUrl(Variable $variable): string
    {
        $existing = $this->variables->allByVariable()->get($variable->id)?->keys() ?? collect();

        return route('extended-translation.admin.shop.variables.edit', [
            $variable,
            $this->locales->firstTargetLocale($existing),
        ]);
    }

    /**
     * @param  Collection<int, mixed>  $categories
     * @return Collection<int, Category>
     */
    protected function flattenCategories(Collection $categories): Collection
    {
        return $categories->flatMap(function ($category) {
            if (! $category instanceof Category) {
                return [];
            }

            $nested = $category->relationLoaded('categories')
                ? $this->flattenCategories($category->categories)
                : collect();

            return collect([$category])->concat($nested);
        });
    }

    /**
     * @param  Collection<int, mixed>  $categories
     * @return Collection<int, Package>
     */
    protected function flattenPackages(Collection $categories): Collection
    {
        return $categories->flatMap(function ($category) {
            if (! $category instanceof Category) {
                return [];
            }

            $packages = $category->relationLoaded('packages')
                ? $category->packages
                : collect();

            $nested = $category->relationLoaded('categories')
                ? $this->flattenPackages($category->categories)
                : collect();

            return $packages->concat($nested);
        });
    }
}
