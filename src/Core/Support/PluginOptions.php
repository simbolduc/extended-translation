<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Support;

class PluginOptions
{
    /**
     * Whether Translate buttons should be injected into Azuriom's own admin pages.
     */
    public static function injectCoreAdmin(): bool
    {
        $value = setting('extended-translation.inject_core_admin');

        if ($value === null || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
