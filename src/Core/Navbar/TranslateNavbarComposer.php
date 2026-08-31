<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Navbar;

use Azuriom\Models\NavbarElement;
use Azuriom\Plugin\ExtendedTranslation\Core\Navbar\NavbarTranslator;
use Illuminate\Support\Enumerable;
use Illuminate\View\View;

/**
 * Overlay navbar item labels for the visitor locale after Azuriom has loaded $navbar.
 *
 * Posts and pages use Eloquent retrieved hooks. Navbar items cannot: Azuriom caches
 * them as arrays, then hydrates models. A retrieved hook would translate before
 * that snapshot is stored, so one visitor's language would land in the shared cache.
 * Translating here runs after cache load, per request, and leaves the cache original.
 */
class TranslateNavbarComposer
{
    public function __construct(
        private NavbarTranslator $navbar,
    ) {
        //
    }

    public function compose(View $view): void
    {
        if (! $this->navbar->shouldApply() || ! isset($view['navbar'])) {
            return;
        }

        $this->translate($view['navbar']);
    }

    protected function translate(mixed $value): void
    {
        if ($value instanceof NavbarElement) {
            $this->navbar->apply($value);

            if ($value->relationLoaded('elements')) {
                foreach ($value->elements as $child) {
                    $this->navbar->apply($child);
                }
            }

            return;
        }

        if ($value instanceof Enumerable || is_array($value)) {
            foreach ($value as $item) {
                $this->translate($item);
            }
        }
    }
}
