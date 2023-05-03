Creating a custom field value handler
=====================================

Creating custom field handlers
------------------------------

To develop custom field handler, end developer needs to implement `CustomFieldHandlerInterface`
and define custom action as service tagged with `netgen_information_collection.field_handler.custom`.

Field handler example:

.. code-block:: php

    <?php

    namespace Acme\Bundle\AcmeBundle\MyCustomFieldHandlers;

    use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
    use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
    use Ibexa\Core\FieldType\Value;
    use Ibexa\Core\FieldType\Integer\Value as IntegerValue;

    class IntegerFieldHandler implements CustomFieldHandlerInterface
    {
        public function supports(Value $value): bool
        {
            return $value instanceof IntegerValue;
        }

        public function toString(Value $value, FieldDefinition $fieldDefinition): string
        {
            // do some magic ..
        }
    }

And service definition:

.. code-block:: yaml

    acme_bundle.my_custom_handlers.integer:
        class: Acme\Bundle\AcmeBundle\MyCustomFieldHandlers\IntegerFieldHandler
        tags:
            - { name: netgen_information_collection.field_handler.custom }

