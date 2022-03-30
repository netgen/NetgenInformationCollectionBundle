<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\FieldType\Time\Value as TimeValue;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class TimeFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof TimeValue;
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
        return new LegacyData($fieldDefinition->id, 0, $value->time, '');
    }

    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
    }
}
