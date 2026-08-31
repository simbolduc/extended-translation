<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Navbar;

use Azuriom\Models\NavbarElement;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Navbar\NavbarTranslator;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\PluginOptions;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminNavbarComposer
{
    public function __construct(
        private LocaleCatalog $locales,
        private NavbarTranslator $translator,
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
            'indexUrl' => route('extended-translation.admin.navbar.index'),
        ];

        if (isset($data['navbarElement']) && $data['navbarElement'] instanceof NavbarElement) {
            $payload['elementId'] = $data['navbarElement']->id;
            $payload['editUrl'] = $this->editUrl($data['navbarElement']);
        }

        $elements = collect();

        if (isset($data['navbarElements'])) {
            $elements = $this->flatten($data['navbarElements']);
        }

        if ($elements->isNotEmpty()) {
            $payload['elements'] = $elements
                ->filter(fn ($element) => $element instanceof NavbarElement)
                ->mapWithKeys(fn (NavbarElement $element) => [$element->id => $this->editUrl($element)])
                ->all();
        }

        $view->getFactory()->startPush(
            'footer-scripts',
            view('extended-translation::admin.inject', ['payload' => $payload])->render()
        );
    }

    /**
     * @param  mixed  $navbarElements
     * @return Collection<int, NavbarElement>
     */
    protected function flatten(mixed $navbarElements): Collection
    {
        return Collection::make($navbarElements)->flatMap(function ($element) {
            if (! $element instanceof NavbarElement) {
                return [];
            }

            $children = $element->relationLoaded('elements')
                ? $element->elements
                : collect();

            return collect([$element])->concat($children);
        });
    }

    protected function editUrl(NavbarElement $element): string
    {
        $existing = $this->translator->allByElement()->get($element->id)?->keys() ?? collect();

        return route('extended-translation.admin.navbar.edit', [
            $element,
            $this->locales->firstTargetLocale($existing),
        ]);
    }
}
