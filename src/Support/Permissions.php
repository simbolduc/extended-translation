<?php

namespace Azuriom\Plugin\ExtendedTranslation\Support;

final class Permissions
{
    public const POSTS = 'extended-translation.posts';

    public const PAGES = 'extended-translation.pages';

    public const NAVBAR = 'extended-translation.navbar';

    public const SETTINGS = 'extended-translation.settings';

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            self::POSTS => 'extended-translation::admin.permissions.posts',
            self::PAGES => 'extended-translation::admin.permissions.pages',
            self::NAVBAR => 'extended-translation::admin.permissions.navbar',
            self::SETTINGS => 'extended-translation::admin.permissions.settings',
        ];
    }
}
