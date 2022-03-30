<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Date\Value as DateValue;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class DateFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof DateValue && $value->date !== null;
    }

    /**
     * @inheritDoc
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition)
    {
        return (string)$value;
    }

    /**
     * @inheritDoc
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition)
    {
        return new LegacyData($fieldDefinition->id, 0, $value->date->getTimestamp(), '');
    }

    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
    }
}
