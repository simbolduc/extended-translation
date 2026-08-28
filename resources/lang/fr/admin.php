<?php

return [
    'nav' => [
        'title' => 'Traductions',
        'posts' => 'Articles',
        'pages' => 'Pages',
        'navbar' => 'Barre de navigation',
        'settings' => 'Paramètres',
    ],

    'title' => 'Traduire les articles',
    'subtitle' => 'Ajoutez une traduction pour chaque langue. Les visiteurs voient l’article original lorsqu’une traduction manque.',
    'empty' => 'Aucun article pour le moment. Créez d’abord un article, puis traduisez-le ici.',
    'original' => 'Original',
    'source' => 'Article source',
    'missing' => 'Manquante',
    'done' => 'Traduit',
    'stale' => 'L’original a été modifié après cette traduction',
    'edit_original' => 'Modifier l’original',
    'start_from_original' => 'Copier l’original dans ce formulaire',
    'delete_translation' => 'Supprimer cette traduction',
    'translate_to' => 'Traduction (:locale)',
    'languages' => 'Langues',
    'no_locales' => 'Sélectionnez au moins une langue dans les paramètres du plugin.',

    'pages' => [
        'title' => 'Traduire les pages',
        'subtitle' => 'Ajoutez une traduction pour chaque langue. Les visiteurs voient la page originale lorsqu’une traduction manque.',
        'empty' => 'Aucune page pour le moment. Créez d’abord une page, puis traduisez-la ici.',
        'source' => 'Page source',
    ],

    'navbar' => [
        'title' => 'Traduire la barre de navigation',
        'subtitle' => 'Traduisez le libellé de chaque élément du menu. Les visiteurs voient le nom original lorsqu’une traduction manque.',
        'empty' => 'Aucun élément de menu pour le moment. Créez-en un dans les paramètres de la barre de navigation, puis traduisez-le ici.',
        'source' => 'Libellé original',
    ],

    'settings' => [
        'title' => 'Paramètres',
        'help' => 'Choisissez les langues proposées pour traduire les articles, les pages et les éléments du menu, ainsi que sur la page publique de choix de langue et dans le menu déroulant des thèmes.',
        'languages_heading' => 'Langues',
        'available' => 'Langues disponibles',
        'default' => 'Langue du site',
        'inject_heading' => 'Pages d’administration Azuriom',
        'inject_help' => 'Lorsque cette option est activée, des boutons Traduire sont ajoutés aux écrans originaux Articles, Pages et Barre de navigation d’Azuriom. Désactivez-la si une mise à jour d’Azuriom modifie ces pages et que les boutons posent problème. Les pages de traduction du plugin continuent de fonctionner dans tous les cas.',
        'inject_label' => 'Ajouter les boutons Traduire aux pages d’administration originales d’Azuriom',
    ],

    'actions' => [
        'translate' => 'Traduire',
        'save' => 'Enregistrer la traduction',
    ],

    'fields' => [
        'status' => 'Traductions',
        'language' => 'Langue',
    ],

    'logs' => [
        'updated' => 'Traduction de l’article #:id mise à jour (:locale)',
        'deleted' => 'Traduction de l’article #:id supprimée (:locale)',
        'navbar_updated' => 'Traduction du menu #:id mise à jour (:locale)',
        'navbar_deleted' => 'Traduction du menu #:id supprimée (:locale)',
        'pages_updated' => 'Traduction de la page #:id mise à jour (:locale)',
        'pages_deleted' => 'Traduction de la page #:id supprimée (:locale)',
    ],
];
