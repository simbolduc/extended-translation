<?php

namespace Azuriom\Plugin\ExtendedTranslation\View\Composers;

use Azuriom\Models\Page;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\PageTranslator;
use Azuriom\Plugin\ExtendedTranslation\Support\Permissions;
use Azuriom\Plugin\ExtendedTranslation\Support\PluginOptions;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminPagesComposer
{
    public function __construct(
        private LocaleCatalog $locales,
        private PageTranslator $translator,
    ) {
        //
    }

    public function compose(View $view): void
    {
        if (! PluginOptions::injectCoreAdmin() || ! Gate::allows(Permissions::PAGES)) {
            return;
        }

        $data = $view->getData();
        $payload = [
            'label' => trans('extended-translation::admin.actions.translate'),
            'indexUrl' => route('extended-translation.admin.pages.index'),
        ];

        if (isset($data['page']) && $data['page'] instanceof Page) {
            $payload['pageId'] = $data['page']->id;
            $payload['editUrl'] = $this->editUrl($data['page']);
        }

        if (isset($data['pages'])) {
            $pages = $data['pages'] instanceof Paginator
                ? collect($data['pages']->items())
                : Collection::make($data['pages']);

            $payload['pages'] = $pages
                ->filter(fn ($page) => $page instanceof Page)
                ->mapWithKeys(fn (Page $page) => [$page->id => $this->editUrl($page)])
                ->all();
        }

        $view->getFactory()->startPush(
            'footer-scripts',
            view('extended-translation::admin.inject', ['payload' => $payload])->render()
        );
    }

    protected function editUrl(Page $page): string
    {
        $existing = $this->translator->allByPage()->get($page->id)?->keys() ?? collect();

        return route('extended-translation.admin.pages.edit', [
            $page,
            $this->locales->firstTargetLocale($existing),
        ]);
    }
}
