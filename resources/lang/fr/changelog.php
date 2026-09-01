<?php

return [
    'nav' => 'Changelog',

    'title' => 'Traduire le changelog',
    'subtitle' => 'Traduisez le titre de la page, les noms de catégories ainsi que les noms et contenus des mises à jour. Les visiteurs voient le texte original lorsqu’une traduction manque.',
    'empty' => 'Aucune catégorie ni mise à jour de changelog pour le moment. Créez-les dans le plugin Changelog, puis traduisez-les ici.',

    'title_row' => [
        'label' => 'Titre de la page',
        'source' => 'Titre source',
        'type' => 'Paramètres',
    ],

    'updates' => [
        'source' => 'Mise à jour source',
        'type' => 'Mise à jour',
    ],

    'categories' => [
        'source' => 'Catégorie source',
        'type' => 'Catégorie',
    ],

    'permissions' => [
        'changelog' => 'Traduire les catégories, mises à jour et le titre de page du changelog',
    ],

    'logs' => [
        'update_updated' => 'Traduction de la mise à jour du changelog #:id mise à jour (:locale)',
        'update_deleted' => 'Traduction de la mise à jour du changelog #:id supprimée (:locale)',
        'category_updated' => 'Traduction de la catégorie du changelog #:id mise à jour (:locale)',
        'category_deleted' => 'Traduction de la catégorie du changelog #:id supprimée (:locale)',
        'title_updated' => 'Traduction du titre de page du changelog #:id mise à jour (:locale)',
        'title_deleted' => 'Traduction du titre de page du changelog #:id supprimée (:locale)',
    ],
];
