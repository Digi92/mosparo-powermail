<?php
declare(strict_types=1);

defined('TYPO3') or die();

$lll = 'LLL:EXT:mosparo_powermail/Resources/Private/Language/locallang_db.xlf:';

// Implement a custom field that allows project selection
$columns = [
    'tx_mosparopowermail_selected_project' => [
        'label' => $lll . 'tx_powermail_domain_model_field.tx_mosparopowermail_selected_project.label',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [],
            'itemsProcFunc' => Mahou\MosparoPowermail\UserFunctions\FormEngine\ProjectsItemsProcFunc::class . '->itemsProcFunc',
            'default' => 'default'
        ]
    ]
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_powermail_domain_model_field',
    $columns
);
$GLOBALS['TCA']['tx_powermail_domain_model_field']['palettes']['mosparoCaptcha'] = [
    'showitem' => 'tx_mosparopowermail_selected_project '
];

// Remove unused field properties and add a select project property for mosparo captcha fields
$GLOBALS['TCA']['tx_powermail_domain_model_field']['types']['mosparoCaptcha'] = [
    'showitem' => 'page, title, type, ' .
        '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_field.sheet1, ' .
        '--palette--;' . $lll . 'tx_powermail_domain_model_field.palette.mosparoCaptcha.label;mosparoCaptcha, ' .
        '--palette--;Layout;43, ' .
        '--palette--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_field.marker_title;5, ' .
        '--div--;LLL:EXT:powermail/Resources/Private/Language/locallang_db.xlf:tabs.access, ' .
        'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime'
];

