<?php

namespace Azuriom\Plugin\ExtendedTranslation\View\Composers;

use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Illuminate\View\View;

class LanguageSelectorComposer
{
    public function __construct(
        private LocaleCatalog $locales,
    ) {
        //
    }

    public function compose(View $view): void
    {
        $current = $this->locales->current();
        $data = [
            'etCurrentLocale' => $current,
            'etLocaleShort' => $this->locales->shortCode($current),
            'etLocales' => $this->locales->enabled(),
        ];

        if (! $view->offsetExists('etRedirect') && ! $view->offsetExists('redirect')) {
            $data['etRedirect'] = url()->full();
        }

        $view->with($data);
    }
}
