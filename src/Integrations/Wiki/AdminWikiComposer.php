<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\PluginOptions;
use Azuriom\Plugin\Wiki\Models\Category;
use Azuriom\Plugin\Wiki\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminWikiComposer
{
    public function __construct(
        private LocaleCatalog $locales,
        private PageTranslator $pages,
        private CategoryTranslator $categories,
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
            'indexUrl' => route('extended-translation.admin.wiki.index'),
        ];

        if (isset($data['page']) && $data['page'] instanceof Page) {
            $payload['editUrl'] = $this->pageEditUrl($data['page']);
        }

        if (isset($data['category']) && $data['category'] instanceof Category) {
            $payload['editUrl'] = $this->categoryEditUrl($data['category']);
        }

        if (isset($data['categories'])) {
            $categories = $this->flattenCategories(Collection::make($data['categories']));

            $payload['wikiCategories'] = $categories
                ->filter(fn ($category) => $category instanceof Category)
                ->mapWithKeys(fn (Category $category) => [$category->id => $this->categoryEditUrl($category)])
                ->all();

            $payload['wikiPages'] = $this->flattenPages(Collection::make($data['categories']))
                ->filter(fn ($page) => $page instanceof Page)
                ->mapWithKeys(fn (Page $page) => [$page->id => $this->pageEditUrl($page)])
                ->all();
        }

        $view->getFactory()->startPush(
            'footer-scripts',
            view('extended-translation::admin.wiki.inject', ['payload' => $payload])->render()
        );
    }

    protected function pageEditUrl(Page $page): string
    {
        $existing = $this->pages->allByPage()->get($page->id)?->keys() ?? collect();

        return route('extended-translation.admin.wiki.pages.edit', [
            $page,
            $this->locales->firstTargetLocale($existing),
        ]);
    }

    protected function categoryEditUrl(Category $category): string
    {
        $existing = $this->categories->allByCategory()->get($category->id)?->keys() ?? collect();

        return route('extended-translation.admin.wiki.categories.edit', [
            $category,
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
     * @return Collection<int, Page>
     */
    protected function flattenPages(Collection $categories): Collection
    {
        return $categories->flatMap(function ($category) {
            if (! $category instanceof Category) {
                return [];
            }

            $pages = $category->relationLoaded('pages')
                ? $category->pages
                : collect();

            $nested = $category->relationLoaded('categories')
                ? $this->flattenPages($category->categories)
                : collect();

            return $pages->concat($nested);
        });
    }
}
