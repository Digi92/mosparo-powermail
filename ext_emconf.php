<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'mosparo integration for EXT:powermail',
    'description' => 'Integrates the mosparo spam protection system into TYPO3 Powermail extension, enabling easy and effective spam protection for Powermail forms.',
    'category' => 'plugin',
    'state' => 'stable',
    'author' => 'Sascha Zander',
    'author_email' => 'sascha.zander@denkwerk.com',
    'version' => '1.0.2',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.4.99',
            'typo3' => '12.4.0-13.4.99',
            'mosparo-form' => '1.0.0-1.99.99',
            'powermail' => '12.5.0-13.99.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
