<?php

namespace Azuriom\Plugin\ExtendedTranslation\View\Composers;

use Azuriom\Models\NavbarElement;
use Azuriom\Models\Page;
use Azuriom\Models\Post;
use Azuriom\Plugin\ExtendedTranslation\Support\NavbarTranslator;
use Azuriom\Plugin\ExtendedTranslation\Support\PageTranslator;
use Azuriom\Plugin\ExtendedTranslation\Support\PostTranslator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Enumerable;
use Illuminate\View\View;

class TranslatePostsComposer
{
    public function __construct(
        private PostTranslator $posts,
        private PageTranslator $pages,
        private NavbarTranslator $navbar,
    ) {
        //
    }

    public function compose(View $view): void
    {
        if (! $this->posts->shouldApply()) {
            return;
        }

        foreach ($view->getData() as $value) {
            $this->translate($value);
        }
    }

    protected function translate(mixed $value): void
    {
        if ($value instanceof Post) {
            $this->posts->apply($value);

            return;
        }

        if ($value instanceof Page) {
            $this->pages->apply($value);

            return;
        }

        if ($value instanceof NavbarElement) {
            $this->navbar->apply($value);

            if ($value->relationLoaded('elements')) {
                foreach ($value->elements as $child) {
                    $this->navbar->apply($child);
                }
            }

            return;
        }

        if ($value instanceof Paginator) {
            foreach ($value->items() as $item) {
                $this->translate($item);
            }

            return;
        }

        if ($value instanceof Enumerable || is_array($value)) {
            foreach ($value as $item) {
                $this->translate($item);
            }
        }
    }
}
