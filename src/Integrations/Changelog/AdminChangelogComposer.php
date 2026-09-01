<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Plugin\Changelog\Models\Category;
use Azuriom\Plugin\Changelog\Models\Update;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\PluginOptions;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminChangelogComposer
{
    public function __construct(
        private LocaleCatalog $locales,
        private UpdateTranslator $updates,
        private CategoryTranslator $categories,
        private TitleTranslator $titles,
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
            'indexUrl' => route('extended-translation.admin.changelog.index'),
        ];

        if (isset($data['update']) && $data['update'] instanceof Update) {
            $payload['editUrl'] = $this->updateEditUrl($data['update']);
        }

        if (isset($data['category']) && $data['category'] instanceof Category) {
            $payload['editUrl'] = $this->categoryEditUrl($data['category']);
        }

        if (isset($data['categories'])) {
            $payload['changelogCategories'] = Collection::make($data['categories'])
                ->filter(fn ($category) => $category instanceof Category)
                ->mapWithKeys(fn (Category $category) => [$category->id => $this->categoryEditUrl($category)])
                ->all();
        }

        if (isset($data['updates'])) {
            $updates = $data['updates'] instanceof Paginator
                ? collect($data['updates']->items())
                : Collection::make($data['updates']);

            $payload['changelogUpdates'] = $updates
                ->filter(fn ($update) => $update instanceof Update)
                ->mapWithKeys(fn (Update $update) => [$update->id => $this->updateEditUrl($update)])
                ->all();
        }

        if (array_key_exists('title', $data) && is_string($data['title'])) {
            $payload['titleUrl'] = $this->titleEditUrl();
        }

        $view->getFactory()->startPush(
            'footer-scripts',
            view('extended-translation::admin.changelog.inject', ['payload' => $payload])->render()
        );
    }

    protected function updateEditUrl(Update $update): string
    {
        $existing = $this->updates->allByUpdate()->get($update->id)?->keys() ?? collect();

        return route('extended-translation.admin.changelog.updates.edit', [
            $update,
            $this->locales->firstTargetLocale($existing),
        ]);
    }

    protected function categoryEditUrl(Category $category): string
    {
        $existing = $this->categories->allByCategory()->get($category->id)?->keys() ?? collect();

        return route('extended-translation.admin.changelog.categories.edit', [
            $category,
            $this->locales->firstTargetLocale($existing),
        ]);
    }

    protected function titleEditUrl(): string
    {
        $existing = $this->titles->allByLocale()->keys();

        return route('extended-translation.admin.changelog.title.edit', [
            $this->locales->firstTargetLocale($existing),
        ]);
    }
}
