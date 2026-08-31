<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Locale;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(
        private LocaleCatalog $locales,
    ) {
        //
    }

    /**
     * Cookie/session only persist the choice. This is what Azuriom reads via app()->getLocale().
     */
    public function handle(Request $request, Closure $next): Response
    {
        $resolved = $this->locales->persisted($request);

        if ($resolved !== null) {
            $this->locales->apply($resolved);
        }

        return $next($request);
    }
}
