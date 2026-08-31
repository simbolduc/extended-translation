<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Wiki\Models\Category;
use Azuriom\Plugin\Wiki\Models\Page;
use Illuminate\View\View;

class WikiController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private PageTranslator $pages,
        private CategoryTranslator $categories,
    ) {
        //
    }

    /**
     * Display the wiki categories and pages that can be translated.
     */
    public function index(): View
    {
        $categories = Category::parents()
            ->with(['categories.pages', 'pages'])
            ->orderBy('position')
            ->get();

        return view('extended-translation::admin.wiki.index', [
            'rows' => $this->rows($categories),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'pageTranslations' => $this->pages->allByPage(),
            'categoryTranslations' => $this->categories->allByCategory(),
        ]);
    }

    /**
     * @param  iterable<int, Category>  $categories
     * @return list<array{type: 'category'|'page', model: Category|Page, depth: int}>
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

            foreach ($category->pages as $page) {
                $rows[] = [
                    'type' => 'page',
                    'model' => $page,
                    'depth' => $depth + 1,
                ];
            }
        }

        return $rows;
    }
}
