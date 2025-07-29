<?php
declare(strict_types=1);

defined('TYPO3') or die();

// Add projects select to powermail fields
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][In2code\Powermail\Domain\Model\Field::class] = [
    'className' => Mahou\MosparoPowermail\Domain\Model\Powermail\Field::class,
];

