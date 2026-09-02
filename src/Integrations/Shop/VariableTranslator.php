<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Azuriom\Plugin\Shop\Models\Variable;
use Illuminate\Support\Collection;
use WeakMap;

class VariableTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, ShopVariableTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Variable, array{description: string, options: array<int, mixed>|null}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the shop variable's visible label and dropdown option names.
     */
    public function apply(Variable $variable): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$variable])) {
            $this->originals[$variable] = [
                'description' => (string) ($variable->getAttributes()['description'] ?? ''),
                'options' => is_array($variable->options) ? $variable->options : null,
            ];
        }

        $original = $this->originals[$variable];
        $translation = $this->forVariable($variable, $this->locales->current());
        $description = $translation?->description;

        $variable->setAttribute(
            'description',
            is_string($description) && $description !== '' ? $description : $original['description']
        );
        $variable->syncOriginalAttribute('description');

        if ($variable->type !== 'dropdown') {
            return;
        }

        $variable->setAttribute(
            'options',
            $this->overlayOptions($original['options'], $translation?->options)
        );
        $variable->syncOriginalAttribute('options');
    }

    /**
     * @param  array<int, mixed>|null  $original
     * @param  array<int, mixed>|null  $translated
     * @return array<int, mixed>|null
     */
    private function overlayOptions(?array $original, ?array $translated): ?array
    {
        if (! is_array($original)) {
            return $original;
        }

        $names = collect($translated ?? [])
            ->filter(fn ($option) => is_array($option) && array_key_exists('value', $option))
            ->mapWithKeys(fn (array $option) => [
                (string) $option['value'] => $option['name'] ?? null,
            ]);

        return array_map(function ($option) use ($names) {
            if (! is_array($option) || ! array_key_exists('value', $option)) {
                return $option;
            }

            $name = $names->get((string) $option['value']);

            if (is_string($name) && $name !== '') {
                $option['name'] = $name;
            }

            return $option;
        }, $original);
    }

    private function forVariable(Variable $variable, ?string $locale = null): ?ShopVariableTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByVariable()->get($variable->id);

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
     * @return Collection<int, Collection<string, ShopVariableTranslation>>
     */
    public function allByVariable(): Collection
    {
        return $this->translations ??= ShopVariableTranslation::query()
            ->get()
            ->groupBy('shop_variable_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
