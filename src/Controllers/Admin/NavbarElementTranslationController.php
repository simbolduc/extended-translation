<?php

namespace Azuriom\Plugin\ExtendedTranslation\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Models\NavbarElement;
use Azuriom\Plugin\ExtendedTranslation\Http\Requests\Admin\NavbarElementTranslationRequest;
use Azuriom\Plugin\ExtendedTranslation\Models\NavbarElementTranslation;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\NavbarTranslator;
use Azuriom\Plugin\ExtendedTranslation\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NavbarElementTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private NavbarTranslator $translator,
    ) {
        $this->middleware('can:'.Permissions::NAVBAR);
    }

    /**
     * Display the navbar elements that can be translated.
     */
    public function index(): View
    {
        $parents = NavbarElement::query()
            ->with('elements')
            ->scopes('parent')
            ->orderBy('position')
            ->get();

        $elements = $parents->flatMap(function (NavbarElement $element) {
            return collect([$element])->concat($element->elements);
        });

        return view('extended-translation::admin.navbar.index', [
            'elements' => $elements,
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translations' => $this->translator->allByElement(),
        ]);
    }

    /**
     * Show the form for translating a navbar element.
     */
    public function edit(NavbarElement $navbarElement, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = NavbarElementTranslation::query()
            ->where('navbar_element_id', $navbarElement->id)
            ->where('locale', $locale)
            ->first();

        $existing = NavbarElementTranslation::query()
            ->where('navbar_element_id', $navbarElement->id)
            ->pluck('locale');

        return view('extended-translation::admin.navbar.edit', [
            'element' => $navbarElement,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a navbar element translation.
     */
    public function update(NavbarElementTranslationRequest $request, NavbarElement $navbarElement, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        NavbarElementTranslation::query()->updateOrCreate(
            [
                'navbar_element_id' => $navbarElement->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.navbar.updated', $navbarElement, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.navbar.edit', [$navbarElement, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a navbar element translation.
     */
    public function destroy(NavbarElement $navbarElement, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        NavbarElementTranslation::query()
            ->where('navbar_element_id', $navbarElement->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.navbar.deleted', $navbarElement, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.navbar.edit', [$navbarElement, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
