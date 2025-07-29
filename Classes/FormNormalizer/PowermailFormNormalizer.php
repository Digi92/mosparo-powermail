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

namespace Mahou\MosparoPowermail\FormNormalizer;

use Denkwerk\MosparoForm\Domain\Model\Dto\NormalizedData;
use Denkwerk\MosparoForm\Domain\Model\Form\MosparoFormDefinitionInterface;
use Mahou\MosparoPowermail\Domain\Model\Form\PowermailMosparoFormDefinition;
use Denkwerk\MosparoForm\FormNormalizer\FormNormalizerInterface;
use Mahou\MosparoPowermail\Event\ModifyPowermailFormFieldTypeListsEvent;
use In2code\Powermail\Domain\Model\Field as PowermailField;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;

/**
 * Class PowermailFormNormalizer
 * @package Mahou\MosparoPowermail\FormNormalizer
 */
class PowermailFormNormalizer implements FormNormalizerInterface
{
    /**
     * @var string[]
     */
    protected array $ignoredFieldTypes = [
        'mosparoCaptcha',
        'password',
        'check',
        'radio',
        'submit',
        'captcha',
        'reset',
        'content',
        'html',
        'file',
        'hidden',
        'typoscript'
    ];

    /**
     * @var string[]
     */
    protected array $verifiableFieldTypes = [
        'input',
        'text',
        'textarea',
        'select',
        'date',
        'country',
        'location',
        '__hp',
    ];

    public function __construct(
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->initializeFieldTypesWithEvent();
    }

    /**
     * Allow other extensions to modify the ignored and verifiable field type lists
     *
     * @return void
     */
    protected function initializeFieldTypesWithEvent(): void
    {
        // Allow other extensions to modify the ignored and verifiable field type lists
        $event = new ModifyPowermailFormFieldTypeListsEvent(
            $this->ignoredFieldTypes,
            $this->verifiableFieldTypes
        );
        $this->eventDispatcher->dispatch($event);

        $this->ignoredFieldTypes = $event->getIgnoredFieldTypes();
        $this->verifiableFieldTypes = $event->getVerifiableFieldTypes();
    }

    /**
     * @param MosparoFormDefinitionInterface|null $formDefinition
     * @return bool
     */
    public function supports(?MosparoFormDefinitionInterface $formDefinition): bool
    {
        return $formDefinition instanceof PowermailMosparoFormDefinition;
    }

    /**
     * This function converts $_POST data into a structured object for mosparo backend verification and
     * provides the $requiredFields and $verifiableFields list to confirm that all fields were verified
     * in the frontend request
     * This function is used for all requests that used Powermail
     *
     * @param array<int|string, mixed> $postData The $_POST variable value
     * @param MosparoFormDefinitionInterface|null $formDefinition
     * @return NormalizedData
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function normalize(array $postData, ?MosparoFormDefinitionInterface $formDefinition): NormalizedData
    {
        $data = new NormalizedData();

        if ($formDefinition instanceof PowermailMosparoFormDefinition &&
            isset($postData['tx_powermail_pi1']['field']) &&
            is_array($postData['tx_powermail_pi1']['field']) &&
            count($postData['tx_powermail_pi1']['field']) > 0
        ) {
            $fieldDefinitions = [];
            foreach ($formDefinition->getPowermailForm()->getPages() as $page) {
                /** @var PowermailField $field */
                foreach ($page->getFields() as $field) {
                    $fieldDefinitions[$field->getMarker()] = $field;
                }
            }

            foreach ($postData['tx_powermail_pi1']['field'] as $fieldIdentifier => $value) {
                $fieldDefinition = $fieldDefinitions[$fieldIdentifier] ?? null;
                $key = 'tx_powermail_pi1[field][' . (string)$fieldIdentifier . ']';

                // Just add core and honeypot fields like "__state" or "__currentPage".
                if (!$fieldDefinition instanceof PowermailField) {
                    $data->addFormData($key, $value);
                    continue;
                }

                if (in_array($fieldDefinition->getType(), $this->ignoredFieldTypes, true)) {
                    continue;
                }

                $data->addFormData($key, $value);

                if ($fieldDefinition->isMandatory()) {
                    $data->addRequiredField($key);
                }

                if (in_array($fieldDefinition->getType(), $this->verifiableFieldTypes, true)) {
                    $data->addVerifiableField($key);
                }
            }
        }

        return $data;
    }
}
