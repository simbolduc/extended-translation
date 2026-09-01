<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Changelog\Models\Category;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Illuminate\View\View;

class ChangelogController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private UpdateTranslator $updates,
        private CategoryTranslator $categories,
        private TitleTranslator $titles,
    ) {
        //
    }

    /**
     * Display the changelog title, categories, and updates that can be translated.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->orderBy('position')
            ->with('updates')
            ->get();

        return view('extended-translation::admin.changelog.index', [
            'title' => setting('changelog.title', 'Changelog'),
            'rows' => $this->rows($categories),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'titleTranslations' => $this->titles->allByLocale(),
            'updateTranslations' => $this->updates->allByUpdate(),
            'categoryTranslations' => $this->categories->allByCategory(),
        ]);
    }

    /**
     * @param  iterable<int, Category>  $categories
     * @return list<array{type: 'category'|'update', model: Category|\Azuriom\Plugin\Changelog\Models\Update, depth: int}>
     */
    protected function rows(iterable $categories): array
    {
        $rows = [];

        foreach ($categories as $category) {
            $rows[] = [
                'type' => 'category',
                'model' => $category,
                'depth' => 0,
            ];

            foreach ($category->updates as $update) {
                $rows[] = [
                    'type' => 'update',
                    'model' => $update,
                    'depth' => 1,
                ];
            }
        }

        return $rows;
    }
}
