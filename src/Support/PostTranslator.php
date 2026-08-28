<?php

namespace Azuriom\Plugin\ExtendedTranslation\Support;

use Azuriom\Models\Post;
use Azuriom\Plugin\ExtendedTranslation\Models\PostTranslation;
use Illuminate\Support\Collection;
use WeakMap;

class PostTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, PostTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Post, array{title: string, description: string, content: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the post's visible fields with the translation for the current locale.
     */
    public function apply(Post $post): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$post])) {
            $attributes = $post->getAttributes();

            $this->originals[$post] = [
                'title' => $attributes['title'] ?? '',
                'description' => $attributes['description'] ?? '',
                'content' => $attributes['content'] ?? '',
            ];
        }

        $original = $this->originals[$post];
        $translation = $this->forPost($post, $this->locales->current());

        foreach (['title', 'description', 'content'] as $field) {
            $value = $translation?->{$field};

            $post->setAttribute(
                $field,
                is_string($value) && $value !== '' ? $value : $original[$field]
            );
            $post->syncOriginalAttribute($field);
        }
    }

    public function forPost(Post $post, ?string $locale = null): ?PostTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByPost()->get($post->id);

        if ($byLocale === null) {
            return null;
        }

        foreach ($candidates as $candidate) {
            $translation = $byLocale->get($candidate);

            if ($translation !== null) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Collection<string, PostTranslation>>
     */
    public function allByPost(): Collection
    {
        return $this->translations ??= PostTranslation::query()
            ->get()
            ->groupBy('post_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
