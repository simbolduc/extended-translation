<?php

return [
    'nav' => 'Wiki',

    'title' => 'Translate wiki',
    'subtitle' => 'Translate category names and page titles and content. Visitors see the original text when a translation is missing. Wiki search still uses the original language.',
    'empty' => 'No wiki categories or pages yet. Create them in the Wiki plugin, then translate them here.',

    'pages' => [
        'source' => 'Source page',
        'type' => 'Page',
    ],

    'categories' => [
        'source' => 'Source category',
        'type' => 'Category',
    ],

    'permissions' => [
        'wiki' => 'Translate wiki pages and categories',
    ],

    'logs' => [
        'page_updated' => 'Updated wiki page translation #:id (:locale)',
        'page_deleted' => 'Deleted wiki page translation #:id (:locale)',
        'category_updated' => 'Updated wiki category translation #:id (:locale)',
        'category_deleted' => 'Deleted wiki category translation #:id (:locale)',
    ],
];
