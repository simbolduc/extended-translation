<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core;

use Illuminate\Contracts\Foundation\Application;

interface PluginIntegration
{
    /**
     * Azuriom plugin id this integration depends on.
     */
    public static function pluginId(): string;

    /**
     * Whether the optional plugin is installed and enabled.
     */
    public static function available(): bool;

    public function register(Application $app): void;

    public function boot(Application $app): void;

    /**
     * @return array<string, string> permission => lang key
     */
    public function permissions(): array;

    /**
     * Permissions that should open the Translations admin dropdown.
     *
     * @return list<string>
     */
    public function adminNavPermissions(): array;

    /**
     * Extra items merged into the Translations dropdown, before Settings.
     *
     * @return array<string, array<string, string>>
     */
    public function adminNavItems(): array;
}
