<?php

namespace Azuriom\Plugin\ExtendedTranslation\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Models\Post;
use Azuriom\Plugin\ExtendedTranslation\Http\Requests\Admin\PostTranslationRequest;
use Azuriom\Plugin\ExtendedTranslation\Models\PostTranslation;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\Permissions;
use Azuriom\Plugin\ExtendedTranslation\Support\PostTranslator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private PostTranslator $translator,
    ) {
        $this->middleware('can:'.Permissions::POSTS);
    }

    /**
     * Display the posts that can be translated.
     */
    public function index(): View
    {
        $posts = Post::query()
            ->with('author')
            ->latest('published_at')
            ->paginate();

        $translations = $this->translator->allByPost();

        return view('extended-translation::admin.posts.index', [
            'posts' => $posts,
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translations' => $translations,
        ]);
    }

    /**
     * Show the form for translating a post.
     */
    public function edit(Post $post, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = PostTranslation::query()
            ->where('post_id', $post->id)
            ->where('locale', $locale)
            ->first();

        $existing = PostTranslation::query()
            ->where('post_id', $post->id)
            ->pluck('locale');

        return view('extended-translation::admin.posts.edit', [
            'post' => $post,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a post translation.
     */
    public function update(PostTranslationRequest $request, Post $post, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        PostTranslation::query()->updateOrCreate(
            [
                'post_id' => $post->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.posts.updated', $post, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.posts.edit', [$post, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a post translation.
     */
    public function destroy(Post $post, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        PostTranslation::query()
            ->where('post_id', $post->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.posts.deleted', $post, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.posts.edit', [$post, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
