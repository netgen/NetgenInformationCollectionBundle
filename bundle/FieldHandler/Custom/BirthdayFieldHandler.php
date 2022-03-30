<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use Netgen\Bundle\BirthdayBundle\Core\FieldType\Birthday\Value as BirthdayValue;

class BirthdayFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof BirthdayValue;
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
        return new LegacyData($fieldDefinition->id, 0, 0, (string) $value);
    }

    /**
     * @inheritDoc
     */
    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
        return new BirthdayValue($legacyData->getDataText());
    }
}
