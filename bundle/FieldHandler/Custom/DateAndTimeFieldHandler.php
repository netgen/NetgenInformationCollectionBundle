<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\FieldType\DateAndTime\Value as DateAndTimeValue;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class DateAndTimeFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof DateAndTimeValue && $value->value !== null;
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
        return new LegacyData($fieldDefinition->id, 0, $value->value->getTimestamp(),'');
    }

    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
    }
}
