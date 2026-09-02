<?php

return [
    'nav' => 'Shop',

    'title' => 'Translate shop',
    'subtitle' => 'Translate category names and descriptions, package names and descriptions, offer names, and variable labels. Visitors see the original text when a translation is missing. Prices, slugs, coupon codes, and server commands stay in the original language.',

    'categories' => [
        'source' => 'Source category',
        'type' => 'Category',
    ],

    'packages' => [
        'section' => 'Categories and packages',
        'source' => 'Source package',
        'type' => 'Package',
        'empty' => 'No shop categories or packages yet. Create them in the Shop plugin, then translate them here.',
    ],

    'offers' => [
        'section' => 'Offers',
        'source' => 'Source offer',
        'type' => 'Offer',
        'empty' => 'No shop offers yet. Create them in the Shop plugin, then translate them here.',
    ],

    'variables' => [
        'section' => 'Variables',
        'source' => 'Source variable',
        'type' => 'Variable',
        'options' => 'Dropdown option labels',
        'empty' => 'No shop variables yet. Create them in the Shop plugin, then translate them here.',
    ],

    'permissions' => [
        'shop' => 'Translate shop categories, packages, offers, and variables',
    ],

    'logs' => [
        'package_updated' => 'Updated shop package translation #:id (:locale)',
        'package_deleted' => 'Deleted shop package translation #:id (:locale)',
        'category_updated' => 'Updated shop category translation #:id (:locale)',
        'category_deleted' => 'Deleted shop category translation #:id (:locale)',
        'offer_updated' => 'Updated shop offer translation #:id (:locale)',
        'offer_deleted' => 'Deleted shop offer translation #:id (:locale)',
        'variable_updated' => 'Updated shop variable translation #:id (:locale)',
        'variable_deleted' => 'Deleted shop variable translation #:id (:locale)',
    ],
];
