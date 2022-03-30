<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class StringFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof TextLineValue;
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
        return new LegacyData($fieldDefinition->id, 0, 0, $value->text);
    }

    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
        return new TextLineValue($legacyData->getDataText());
    }
}
