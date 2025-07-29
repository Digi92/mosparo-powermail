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

namespace Mahou\MosparoPowermail\Validation\Validator\SpamShield;

use Mahou\MosparoPowermail\Domain\Model\Form\PowermailMosparoFormDefinition;
use Mahou\MosparoPowermail\Domain\Model\Powermail\Field;
use Denkwerk\MosparoForm\Validation\Validator\MosparoCaptchaValidator;
use In2code\Powermail\Domain\Validator\SpamShield\AbstractMethod;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class MosparoCaptchaMethod
 * @package Mahou\MosparoPowermail\Validation\Validator\SpamShield
 */
class MosparoCaptchaMethod extends AbstractMethod
{
    /**
     * mosparo spam protection
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function spamCheck(): bool
    {
        // Skip if not set or skipped during createAction
        if (!$this->hasMosparoField() || $this->shouldSkipMosaproValidatorCheck()) {
            return false;
        }

        /** @var MosparoCaptchaValidator $captchaValidator */
        $captchaValidator = GeneralUtility::makeInstance(
            MosparoCaptchaValidator::class
        );
        $captchaValidator->setOptions([
            'formDefinition' => new PowermailMosparoFormDefinition($this->mail->getForm()),
            'selectedProject' => $this->getSelectedProject()
        ]);

        $captchaValidationResult = $captchaValidator->validate('');
        if (!$captchaValidationResult->hasErrors()) {
            // No spam
            return false;
        }

        // if spam recognized, return true
        return true;
    }

    /**
     * Checks whether the form contains at least one field of type 'mosparo'
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function hasMosparoField(): bool
    {
        foreach ($this->mail->getForm()->getPages() as $page) {
            foreach ($page->getFields() as $field) {
                if ($field->getType() === 'mosparoCaptcha') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return the selected project from the first mosparo field found in the field list
     *
     * @return string
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected function getSelectedProject(): string
    {
        foreach ($this->mail->getForm()->getPages() as $page) {
            foreach ($page->getFields() as $field) {
                if ($field->getType() === 'mosparoCaptcha' &&
                    $field instanceof Field
                ) {
                    return $field->getTxMosparopowermailSelectedProject();
                }
            }
        }

        return 'default';
    }

    /**
     * Determines whether the mosparo validator can be skipped during createAction,
     * based on a prior confirmationAction where the mosparo validator was already executed
     *
     * @return bool
     */
    protected function shouldSkipMosaproValidatorCheck(): bool
    {
        $skip = false;
        if (property_exists($this, 'flexForm')) {
            $action = $GLOBALS['TYPO3_REQUEST']->getQueryParams()['tx_powermail_pi1']['action'] ?? [];

            if ($action === 'optinConfirm' &&
                $this->flexForm['settings']['flexform']['main']['optin'] === '1'
            ) {
                $skip = true;
            }

            if ((
                    $action === 'create' ||
                    $action === 'checkCreate'
                ) &&
                $this->flexForm['settings']['flexform']['main']['confirmation'] === '1'
            ) {
                $skip = true;
            }
        }
        return $skip;
    }
}
