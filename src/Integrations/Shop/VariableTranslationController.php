<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Shop\Models\Variable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VariableTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private VariableTranslator $translator,
    ) {
        //
    }

    /**
     * Show the form for translating a shop variable.
     */
    public function edit(Variable $shopVariable, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = ShopVariableTranslation::query()
            ->where('shop_variable_id', $shopVariable->id)
            ->where('locale', $locale)
            ->first();

        $existing = ShopVariableTranslation::query()
            ->where('shop_variable_id', $shopVariable->id)
            ->pluck('locale');

        $optionNames = collect($translation?->options ?? [])
            ->filter(fn ($option) => is_array($option) && array_key_exists('value', $option))
            ->mapWithKeys(fn (array $option) => [
                (string) $option['value'] => $option['name'] ?? '',
            ]);

        return view('extended-translation::admin.shop.variables.edit', [
            'variable' => $shopVariable,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
            'optionNames' => $optionNames,
        ]);
    }

    /**
     * Store or update a shop variable translation.
     */
    public function update(VariableTranslationRequest $request, Variable $shopVariable, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $data = $request->validated();
        $data['options'] = $this->translatedOptions($shopVariable, $data['options'] ?? []);

        ShopVariableTranslation::query()->updateOrCreate(
            [
                'shop_variable_id' => $shopVariable->id,
                'locale' => $locale,
            ],
            $data
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.shop.variable.updated', $shopVariable, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.shop.variables.edit', [$shopVariable, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a shop variable translation.
     */
    public function destroy(Variable $shopVariable, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        ShopVariableTranslation::query()
            ->where('shop_variable_id', $shopVariable->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.shop.variable.deleted', $shopVariable, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.shop.variables.edit', [$shopVariable, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Keep dropdown option names whose values still exist on the original variable.
     *
     * @param  array<int, mixed>  $options
     * @return array<int, array{value: string, name: string}>|null
     */
    protected function translatedOptions(Variable $variable, array $options): ?array
    {
        if ($variable->type !== 'dropdown') {
            return null;
        }

        $allowed = collect($variable->options ?? [])
            ->filter(fn ($option) => is_array($option) && array_key_exists('value', $option))
            ->map(fn (array $option) => (string) $option['value']);

        return collect($options)
            ->filter(fn ($option) => is_array($option) && $allowed->contains((string) ($option['value'] ?? '')))
            ->map(fn (array $option) => [
                'value' => (string) $option['value'],
                'name' => (string) ($option['name'] ?? ''),
            ])
            ->values()
            ->all();
    }
}
