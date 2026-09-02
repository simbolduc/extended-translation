<?php

return [
    'nav' => 'Boutique',

    'title' => 'Traduire la boutique',
    'subtitle' => 'Traduisez les noms et descriptions des catégories, les noms et descriptions des produits, les noms des offres et les libellés des variables. Les visiteurs voient le texte original lorsqu’une traduction manque. Les prix, les slugs, les codes promo et les commandes serveur restent dans la langue originale.',

    'categories' => [
        'source' => 'Catégorie source',
        'type' => 'Catégorie',
    ],

    'packages' => [
        'section' => 'Catégories et produits',
        'source' => 'Produit source',
        'type' => 'Produit',
        'empty' => 'Aucune catégorie ni produit de boutique pour le moment. Créez-les dans le plugin Boutique, puis traduisez-les ici.',
        'empty_category' => 'Aucun produit dans cette catégorie.',
    ],

    'offers' => [
        'section' => 'Offres',
        'source' => 'Offre source',
        'type' => 'Offre',
        'empty' => 'Aucune offre de boutique pour le moment. Créez-les dans le plugin Boutique, puis traduisez-les ici.',
    ],

    'variables' => [
        'section' => 'Variables',
        'source' => 'Variable source',
        'type' => 'Variable',
        'options' => 'Libellés des options de liste',
        'empty' => 'Aucune variable de boutique pour le moment. Créez-les dans le plugin Boutique, puis traduisez-les ici.',
    ],

    'permissions' => [
        'shop' => 'Traduire les catégories, produits, offres et variables de la boutique',
    ],

    'logs' => [
        'package_updated' => 'Traduction du produit de boutique #:id mise à jour (:locale)',
        'package_deleted' => 'Traduction du produit de boutique #:id supprimée (:locale)',
        'category_updated' => 'Traduction de la catégorie de boutique #:id mise à jour (:locale)',
        'category_deleted' => 'Traduction de la catégorie de boutique #:id supprimée (:locale)',
        'offer_updated' => 'Traduction de l’offre de boutique #:id mise à jour (:locale)',
        'offer_deleted' => 'Traduction de l’offre de boutique #:id supprimée (:locale)',
        'variable_updated' => 'Traduction de la variable de boutique #:id mise à jour (:locale)',
        'variable_deleted' => 'Traduction de la variable de boutique #:id supprimée (:locale)',
    ],
];
