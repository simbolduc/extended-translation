<?php

namespace Azuriom\Plugin\ExtendedTranslation\View\Composers;

use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Illuminate\View\View;

class LocaleComposer
{
    public function __construct(
        private LocaleCatalog $locales,
    ) {
        //
    }

    /**
     * Apply the visitor locale before any view calls trans().
     */
    public function compose(View $view): void
    {
        $resolved = $this->locales->persisted();

        if ($resolved !== null) {
            $this->locales->apply($resolved);
        }
    }
}
