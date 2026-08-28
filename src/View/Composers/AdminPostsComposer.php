<?php

namespace Azuriom\Plugin\ExtendedTranslation\View\Composers;

use Azuriom\Models\Post;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\Permissions;
use Azuriom\Plugin\ExtendedTranslation\Support\PluginOptions;
use Azuriom\Plugin\ExtendedTranslation\Support\PostTranslator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminPostsComposer
{
    public function __construct(
        private LocaleCatalog $locales,
        private PostTranslator $translator,
    ) {
        //
    }

    public function compose(View $view): void
    {
        if (! PluginOptions::injectCoreAdmin() || ! Gate::allows(Permissions::POSTS)) {
            return;
        }

        $data = $view->getData();
        $payload = [
            'label' => trans('extended-translation::admin.actions.translate'),
            'indexUrl' => route('extended-translation.admin.posts.index'),
        ];

        if (isset($data['post']) && $data['post'] instanceof Post) {
            $payload['postId'] = $data['post']->id;
            $payload['editUrl'] = $this->editUrl($data['post']);
        }

        if (isset($data['posts'])) {
            $posts = $data['posts'] instanceof Paginator
                ? collect($data['posts']->items())
                : Collection::make($data['posts']);

            $payload['posts'] = $posts
                ->filter(fn ($post) => $post instanceof Post)
                ->mapWithKeys(fn (Post $post) => [$post->id => $this->editUrl($post)])
                ->all();
        }

        $view->getFactory()->startPush(
            'footer-scripts',
            view('extended-translation::admin.inject', ['payload' => $payload])->render()
        );
    }

    protected function editUrl(Post $post): string
    {
        $existing = $this->translator->allByPost()->get($post->id)?->keys() ?? collect();

        $locale = $this->locales->firstTargetLocale($existing);

        return route('extended-translation.admin.posts.edit', [$post, $locale]);
    }
}
