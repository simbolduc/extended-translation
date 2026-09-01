<?php

return [
    'nav' => 'Changelog',

    'title' => 'Translate changelog',
    'subtitle' => 'Translate the page title, category names, and update names and content. Visitors see the original text when a translation is missing.',
    'empty' => 'No changelog categories or updates yet. Create them in the Changelog plugin, then translate them here.',

    'title_row' => [
        'label' => 'Page title',
        'source' => 'Source title',
        'type' => 'Settings',
    ],

    'updates' => [
        'source' => 'Source update',
        'type' => 'Update',
    ],

    'categories' => [
        'source' => 'Source category',
        'type' => 'Category',
    ],

    'permissions' => [
        'changelog' => 'Translate changelog categories, updates, and page title',
    ],

    'logs' => [
        'update_updated' => 'Updated changelog update translation #:id (:locale)',
        'update_deleted' => 'Deleted changelog update translation #:id (:locale)',
        'category_updated' => 'Updated changelog category translation #:id (:locale)',
        'category_deleted' => 'Deleted changelog category translation #:id (:locale)',
        'title_updated' => 'Updated changelog page title translation #:id (:locale)',
        'title_deleted' => 'Deleted changelog page title translation #:id (:locale)',
    ],
];
