Field handlers
==============

### Creating custom field handlers

To develop custom field handler, end developer needs to implement `CustomFieldHandlerInterface` 
and define custom action as service tagged with `netgen_information_collection.field_handler.custom`.

Field handler example:

```php
<?php

namespace Acme\Bundle\AcmeBundle\MyCustomFieldHandlers;

use Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom\CustomFieldHandlerInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\FieldType\Integer\Value as IntegerValue;

class IntegerFieldHandler implements CustomFieldHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function supports(Value $value)
    {
        return $value instanceof IntegerValue;
    }

    /**
     * @inheritdoc
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition)
    {
        // do some magic ..
    }
}
```

And service definition:

```yml
acme_bundle.my_custom_handlers.integer:
    class: Acme\Bundle\AcmeBundle\MyCustomFieldHandlers\IntegerFieldHandler
    tags:
        - { name: netgen_information_collection.field_handler.custom }
```
