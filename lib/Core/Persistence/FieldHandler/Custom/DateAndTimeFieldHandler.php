<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\DateAndTime\Value as DateAndTimeValue;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class DateAndTimeFieldHandler implements CustomLegacyFieldHandlerInterface
{
    public function supports(Value $value): bool
    {
        return $value instanceof DateAndTimeValue;
    }

    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        if ($value instanceof DateAndTimeValue) {
            return (string) $value;
        }
    }

    /**
     * @param \Ibexa\Core\FieldType\DateAndTime\Value $value
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withIntValue($fieldDefinition->id, $value->value->getTimestamp());
    }

    public function fromLegacyValue(FieldValue $legacyData)
    {
    }
}
