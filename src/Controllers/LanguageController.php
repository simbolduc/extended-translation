<?php

namespace Azuriom\Plugin\ExtendedTranslation\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\ExtendedTranslation\Http\Requests\LanguageRequest;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
    ) {
        //
    }

    /**
     * Show the public language switcher.
     */
    public function index(Request $request): View
    {
        return view('extended-translation::language', [
            'locales' => $this->locales->enabled(),
            'current' => $this->locales->current(),
            'redirect' => $this->redirectTarget($request),
        ]);
    }

    /**
     * Persist the chosen locale and return to the previous page.
     */
    public function update(LanguageRequest $request): RedirectResponse
    {
        $locale = $request->validated('locale');

        return redirect()
            ->to($this->redirectTarget($request))
            ->withCookie($this->locales->persist($request, $locale));
    }

    /**
     * Persist the locale from a theme dropdown link and return to the previous page.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $resolved = $this->locales->resolveEnabled($locale);

        abort_if($resolved === null, 404);

        return redirect()
            ->to($this->redirectTarget($request))
            ->withCookie($this->locales->persist($request, $resolved));
    }

    protected function redirectTarget(Request $request): string
    {
        $fallback = route('home');
        $candidate = $request->input('redirect');

        if (! is_string($candidate) || $candidate === '') {
            $candidate = url()->previous($fallback);
        }

        return $this->isSafeRedirect($candidate) ? $candidate : $fallback;
    }

    protected function isSafeRedirect(string $url): bool
    {
        $root = rtrim(url('/'), '/');

        if ($url !== $root && ! str_starts_with($url, $root.'/')) {
            return false;
        }

        $path = rtrim((string) parse_url($url, PHP_URL_PATH), '/');
        $languagePath = rtrim((string) parse_url(route('extended-translation.language'), PHP_URL_PATH), '/');

        return $path !== $languagePath;
    }
}
