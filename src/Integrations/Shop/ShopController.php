<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Shop\Models\Category;
use Azuriom\Plugin\Shop\Models\Offer;
use Azuriom\Plugin\Shop\Models\Package;
use Azuriom\Plugin\Shop\Models\Variable;
use Illuminate\View\View;

class ShopController extends Controller
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

    /**
     * Display the shop categories, packages, offers, and variables that can be translated.
     */
    public function index(): View
    {
        $categories = Category::parents()
            ->with(['categories.packages', 'packages'])
            ->get();

        return view('extended-translation::admin.shop.index', [
            'rows' => $this->rows($categories),
            'offers' => Offer::query()->orderBy('id')->get(),
            'variables' => Variable::query()->orderBy('id')->get(),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'categoryTranslations' => $this->categories->allByCategory(),
            'packageTranslations' => $this->packages->allByPackage(),
            'offerTranslations' => $this->offers->allByOffer(),
            'variableTranslations' => $this->variables->allByVariable(),
        ]);
    }

    /**
     * @param  iterable<int, Category>  $categories
     * @return list<array{type: 'category'|'package', model: Category|Package, depth: int}>
     */
    protected function rows(iterable $categories, int $depth = 0): array
    {
        $rows = [];

        foreach ($categories as $category) {
            $rows[] = [
                'type' => 'category',
                'model' => $category,
                'depth' => $depth,
            ];

            if ($category->relationLoaded('categories')) {
                foreach ($this->rows($category->categories, $depth + 1) as $row) {
                    $rows[] = $row;
                }
            }

            foreach ($category->packages as $package) {
                $rows[] = [
                    'type' => 'package',
                    'model' => $package,
                    'depth' => $depth + 1,
                ];
            }
        }

        return $rows;
    }
}
