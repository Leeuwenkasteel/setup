<?php

return [
    'packages' => [
        'auth' => [
            'name' => 'leeuwenkasteel/auth',
            'description' => 'Authenticatie module',
            'dependencies' => ['templates']
        ],
        'templates' => [
            'name' => 'leeuwenkasteel/templates',
            'description' => 'Templates module',
            'dependencies' => []
        ],
        'webshop' => [
            'name' => 'leeuwenkasteel/webshop',
            'description' => 'Webshop module',
            'dependencies' => ['templates']
        ],
        'seo' => [
            'name' => 'leeuwenkasteel/seo',
            'description' => 'SEO module',
            'dependencies' => []
        ],
    ]
];