<?php

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;
use Ibexa\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\BirthdayBundle\Core\FieldType\Birthday\Value as BirthdayValue;

class BirthdayFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value): bool
    {
        return $value instanceof BirthdayValue;
    }

    /**
     * @inheritDoc
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return new FieldValue($fieldDefinition->id, (string) $value, 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function fromLegacyValue(FieldValue $legacyData)
    {
        return new BirthdayValue($legacyData->getDataText());
    }
}
