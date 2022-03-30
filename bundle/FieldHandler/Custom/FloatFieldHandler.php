<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use eZ\Publish\Core\FieldType\Float\Value as FloatValue;

class FloatFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof FloatValue;
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
        return new LegacyData($fieldDefinition->id, $value->value, 0, '');
    }

    /**
     * @inheritDoc
     */
    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
        return new FloatValue($legacyData->getDataFloat());
    }
}
