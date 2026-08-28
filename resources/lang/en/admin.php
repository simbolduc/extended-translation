<?php

return [
    'nav' => [
        'title' => 'Translations',
        'posts' => 'News articles',
        'pages' => 'Pages',
        'navbar' => 'Navbar',
        'settings' => 'Settings',
    ],

    'title' => 'Translate news articles',
    'subtitle' => 'Write a translation for each language. Visitors see the original article when a translation is missing.',
    'empty' => 'No news articles yet. Create a post first, then translate it here.',
    'original' => 'Original',
    'source' => 'Source article',
    'missing' => 'Missing',
    'done' => 'Translated',
    'stale' => 'Original updated after this translation',
    'edit_original' => 'Edit original',
    'start_from_original' => 'Copy original into this form',
    'delete_translation' => 'Delete this translation',
    'translate_to' => 'Translation (:locale)',
    'languages' => 'Languages',
    'no_locales' => 'Select at least one language in the plugin settings.',

    'pages' => [
        'title' => 'Translate pages',
        'subtitle' => 'Write a translation for each language. Visitors see the original page when a translation is missing.',
        'empty' => 'No pages yet. Create a page first, then translate it here.',
        'source' => 'Source page',
    ],

    'navbar' => [
        'title' => 'Translate navbar items',
        'subtitle' => 'Translate the label of each navbar item. Visitors see the original name when a translation is missing.',
        'empty' => 'No navbar items yet. Create one in the navbar settings, then translate it here.',
        'source' => 'Original label',
    ],

    'settings' => [
        'title' => 'Settings',
        'help' => 'Choose the languages that should appear when translating news articles, pages, and navbar items, on the public language page, and in the theme language dropdown.',
        'languages_heading' => 'Languages',
        'available' => 'Available languages',
        'default' => 'Site language',
        'inject_heading' => 'Azuriom admin pages',
        'inject_help' => 'When enabled, Translate buttons are added to Azuriom’s original Posts, Pages, and Navbar screens. Turn this off if an Azuriom update changes those pages and the extra buttons cause problems. The plugin’s own translation pages keep working either way.',
        'inject_label' => 'Inject Translate buttons into Azuriom’s original admin pages',
    ],

    'actions' => [
        'translate' => 'Translate',
        'save' => 'Save translation',
    ],

    'fields' => [
        'status' => 'Translations',
        'language' => 'Language',
    ],

    'logs' => [
        'updated' => 'Updated post translation #:id (:locale)',
        'deleted' => 'Deleted post translation #:id (:locale)',
        'navbar_updated' => 'Updated navbar translation #:id (:locale)',
        'navbar_deleted' => 'Deleted navbar translation #:id (:locale)',
        'pages_updated' => 'Updated page translation #:id (:locale)',
        'pages_deleted' => 'Deleted page translation #:id (:locale)',
    ],
];
