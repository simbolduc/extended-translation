<?php

return [
    'nav' => 'Wiki',

    'title' => 'Traduire le wiki',
    'subtitle' => 'Traduisez les noms de catégories ainsi que les titres et contenus des pages. Les visiteurs voient le texte original lorsqu’une traduction manque. La recherche du wiki utilise toujours la langue originale.',
    'empty' => 'Aucune catégorie ni page de wiki pour le moment. Créez-les dans le plugin Wiki, puis traduisez-les ici.',

    'pages' => [
        'source' => 'Page source',
        'type' => 'Page',
    ],

    'categories' => [
        'source' => 'Catégorie source',
        'type' => 'Catégorie',
    ],

    'permissions' => [
        'wiki' => 'Traduire les pages et catégories du wiki',
    ],

    'logs' => [
        'page_updated' => 'Traduction de la page wiki #:id mise à jour (:locale)',
        'page_deleted' => 'Traduction de la page wiki #:id supprimée (:locale)',
        'category_updated' => 'Traduction de la catégorie wiki #:id mise à jour (:locale)',
        'category_deleted' => 'Traduction de la catégorie wiki #:id supprimée (:locale)',
    ],
];
