<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Wiki\Models\Category;
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
            'categories' => $categories,
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'pageTranslations' => $this->pages->allByPage(),
            'categoryTranslations' => $this->categories->allByCategory(),
        ]);
    }
}
