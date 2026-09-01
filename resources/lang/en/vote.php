<?php

return [
    'nav' => 'Vote',

    'title' => 'Translate vote rewards',
    'subtitle' => 'Translate each reward name. Visitors see the original name when a translation is missing. Vote sites and other reward settings stay in the original language.',
    'empty' => 'No vote rewards yet. Create a reward first, then translate it here.',
    'source' => 'Source reward',

    'permissions' => [
        'rewards' => 'Translate vote reward names',
    ],

    'logs' => [
        'updated' => 'Updated vote reward translation #:id (:locale)',
        'deleted' => 'Deleted vote reward translation #:id (:locale)',
    ],
];
