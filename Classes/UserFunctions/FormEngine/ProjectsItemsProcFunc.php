<?php
declare(strict_types=1);

/*
 * This file is part of the "mosparo-powermail" Extension for TYPO3 CMS.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Mahou\MosparoPowermail\UserFunctions\FormEngine;

use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class ProjectsItemsProcFunc
 * @package Mahou\MosparoPowermail\UserFunctions\FormEngine
 */
class ProjectsItemsProcFunc
{
    /**
     * Adds the configured mosparo projects from TypoScript as select options
     *
     * @param mixed $params
     * @return void
     */
    public function itemsProcFunc(&$params): void
    {
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);

        // Override the global TYPO3 request to simulate a request from the root page of the current site.
        // This ensures that the correct TypoScript setup is loaded for the page where the form element appears,
        // rather than for the page context of the backend module itself.
        if (isset($GLOBALS['TYPO3_REQUEST']) &&
            $GLOBALS['TYPO3_REQUEST'] instanceof ServerRequest &&
            isset($params['site']) &&
            $params['site'] instanceof Site
        ) {
            $configurationManager->setRequest(clone $GLOBALS['TYPO3_REQUEST']->withQueryParams(['id' => $params['site']->getRootPageId()]));
        }

        $fullTypoScript = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        // Get all configured mosparo projects
        $projects = $fullTypoScript['plugin.']['tx_mosparoform.']['settings.']['projects.'] ?? [];

        // Add projects as items
        foreach ($projects as $key => $project) {
            // Remove dot form TypoScript array key
            $value = rtrim((string)$key, '.');
            $label = ucfirst($value);
            $params['items'][] = [$label, $value];
        }
    }
}
