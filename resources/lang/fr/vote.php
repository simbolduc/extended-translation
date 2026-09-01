<?php

return [
    'nav' => 'Vote',

    'title' => 'Traduire les récompenses de vote',
    'subtitle' => 'Traduisez le nom de chaque récompense. Les visiteurs voient le nom original lorsqu’une traduction manque. Les sites de vote et les autres paramètres des récompenses restent dans la langue originale.',
    'empty' => 'Aucune récompense de vote pour le moment. Créez d’abord une récompense, puis traduisez-la ici.',
    'source' => 'Récompense source',

    'permissions' => [
        'rewards' => 'Traduire les noms des récompenses de vote',
    ],

    'logs' => [
        'updated' => 'Traduction de la récompense de vote #:id mise à jour (:locale)',
        'deleted' => 'Traduction de la récompense de vote #:id supprimée (:locale)',
    ],
];
