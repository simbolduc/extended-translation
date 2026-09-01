<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Plugin\Changelog\Models\Update;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Illuminate\Support\Collection;
use WeakMap;

class UpdateTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, ChangelogUpdateTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Update, array{name: string, description: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the changelog update's visible fields with the translation for the current locale.
     */
    public function apply(Update $update): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$update])) {
            $attributes = $update->getAttributes();

            $this->originals[$update] = [
                'name' => $attributes['name'] ?? '',
                'description' => $attributes['description'] ?? '',
            ];
        }

        $original = $this->originals[$update];
        $translation = $this->forUpdate($update, $this->locales->current());

        foreach (['name', 'description'] as $field) {
            $value = $translation?->{$field};

            $update->setAttribute(
                $field,
                is_string($value) && $value !== '' ? $value : $original[$field]
            );
            $update->syncOriginalAttribute($field);
        }
    }

    private function forUpdate(Update $update, ?string $locale = null): ?ChangelogUpdateTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByUpdate()->get($update->id);

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
     * @return Collection<int, Collection<string, ChangelogUpdateTranslation>>
     */
    public function allByUpdate(): Collection
    {
        return $this->translations ??= ChangelogUpdateTranslation::query()
            ->get()
            ->groupBy('changelog_update_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
