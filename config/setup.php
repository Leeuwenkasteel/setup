<?php

return [
    'packages' => [
        'analytics' => [
            'name' => 'analytics',
            'description' => 'Analyseer en volg gebruikersgedrag met gedetailleerde statistieken en rapportages.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'auth' => [
            'name' => 'auth',
            'description' => 'Authenticatiemodule met gebruikersbeheer, rollen en machtigingen.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'contact' => [
            'name' => 'contact',
            'description' => 'Beheer contactformulieren en communicatie tussen gebruikers en beheerders.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'domains' => [
            'name' => 'domains',
            'description' => 'Domeinbeheer voor multisite-functionaliteit en aangepaste domeinen.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'error-logger' => [
            'name' => 'error-logger',
            'description' => 'Log en monitor applicatiefouten en uitzonderingen voor debugging en onderhoud.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'languages' => [
            'name' => 'languages',
            'description' => 'Meertalige ondersteuning met vertalingen en taalbeheer.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'media' => [
            'name' => 'media',
            'description' => 'Beheer en organiseer afbeeldingen, video’s en andere mediabestanden.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'menu' => [
            'name' => 'menu',
            'description' => 'Dynamisch menu-beheer met drag-and-drop functionaliteit en meertalige ondersteuning.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'portfolio' => [
            'name' => 'portfolio',
            'description' => 'Project- en portfolio-beheer.',
            'dependencies' => ['templates'],
			'kind' => 'optional'
        ],
        'schema' => [
            'name' => 'schema',
            'description' => 'SEO-geoptimaliseerde gestructureerde gegevens (Schema.org) voor betere zoekmachine-indexering.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'scholen' => [
            'name' => 'scholen',
            'description' => 'Lijst met school- en scholengemeenschapgegevens.',
            'dependencies' => ['templates'],
			'kind' => 'optional'
        ],
        'seo' => [
            'name' => 'seo',
            'description' => 'Optimaliseer je website voor zoekmachines met meta-tags, sitemaps en structured data.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'templates' => [
            'name' => 'templates',
            'description' => 'Vooraf ontworpen sjablonen en componenten voor een consistente UI/UX.',
            'dependencies' => ['analytics', 'contact', 'domains', 'error-logger', 'languages', 'media', 'menu', 'seo', 'under-construction'],
			'kind' => 'basic'
        ],
        'under-construction' => [
            'name' => 'under-construction',
            'description' => 'Toon een "Website in onderhoud" pagina met optionele timer en contactinformatie.',
            'dependencies' => ['templates'],
			'kind' => 'basic'
        ],
        'webshop' => [
            'name' => 'webshop',
            'description' => 'E-commerce oplossing met producten, bestellingen en betalingsintegraties.',
            'dependencies' => ['templates'],
			'kind' => 'optional'
        ],
    ]
];
