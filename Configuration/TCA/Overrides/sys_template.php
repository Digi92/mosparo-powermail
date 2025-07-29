<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'mosparo_powermail',
    'Configuration/TypoScript',
    'mosparo integration for EXT:powermail'
);
