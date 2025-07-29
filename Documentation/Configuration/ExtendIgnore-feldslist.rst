:navigation-title: Extending Field Type Lists

.. _extending-field-type-lists-powermail:

================================
Extending Field Type Lists in the FormNormalizer
================================

This extension provides a FormNormalizer for the Powermail extension that helps with processing submitted form data.
It allows specific field types to be ignored or verified during validation.

If you are using custom form field types, you need to register them via the event :php:`Mahou\MosparoPowermail\Event\ModifyPowermailFormFieldTypeListsEvent` to ensure proper validation behavior.

..  contents::
    :local:

..  seealso::
    | For details on how mosparo expects form data, see the official documentation:
    | https://documentation.mosparo.io/docs/integration/custom#preparing-form-data

.. _extending-field-type-lists-powermail-event:

ModifyPowermailFormFieldTypeListsEvent
================================
This event is triggered when the FormNormalizer is instantiated.

You can use this event to:

..  confval:: getIgnoredFieldTypes() / setIgnoredFieldTypes()
    :name: modify-form-field-type-lists-event-ignore-list
    :required: false
    :type: array of strings

    Add field types to the ignore list

..  confval:: getVerifiableFieldTypes() / setVerifiableFieldTypes()
    :name: modify-form-field-type-lists-event-verifiable-list
    :required: false
    :type: array of strings

    Add field types to the verifiable list


.. _extending-field-type-lists-powermail-custom:

Adding your own Event Listener
================================
To register your own field types for the FormNormalizer, follow these steps:

#. In your custom provider extension, create a new PHP file at: :file:`Classes/EventListener/ModifyFormFieldTypesForPowermailFormListener.php`
#. | Add the following implementation.
   | This example adds custom field types to both the ignored and verifiable lists:

   ..  code-block:: php
       :caption: EXT:site_package/Classes/EventListener/ModifyFormFieldTypesForPowermailFormListener.php

        namespace Vendor\MyExtension\EventListener;

        use Mahou\MosparoPowermail\Event\ModifyPowermailFormFieldTypeListsEvent;

        final class ModifyFormFieldTypesForPowermailFormListener
        {
            public function __invoke(ModifyPowermailFormFieldTypeListsEvent $event): void
            {
                // Add custom field types to the ignore list
                $ignored = $event->getIgnoredFieldTypes();
                $ignored[] = 'customFormElementToIgnore';
                $event->setIgnoredFieldTypes($ignored);

                // Add custom field types to the verifiable list
                $verifiable = $event->getVerifiableFieldTypes();
                $verifiable[] = 'customFormInputElement';
                $event->setVerifiableFieldTypes($verifiable);
            }
        }
#. Register the listener in your custom provider extension :ref:`t3coreapi:extension-configuration-services-yaml`:

   ..  code-block:: yaml
       :caption: EXT:site_package/Configuration/service.yaml

        ...

        services:
          Vendor\MyExtension\EventListener\ModifyFormFieldTypesForPowermailFormListener:
            tags:
              - name: event.listener
                event: Mahou\MosparoPowermail\Event\ModifyPowermailFormFieldTypeListsEvent


