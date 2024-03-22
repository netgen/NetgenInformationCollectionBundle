<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Time\Value;
use Ibexa\Core\FieldType\Time\Value as TimeValue;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class TimeFieldHandler implements CustomLegacyFieldHandlerInterface
{
    public function supports(ValueInterface $value): bool
    {
        return $value instanceof TimeValue;
    }

    public function toString(ValueInterface $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * @param Value $value
     */
    public function getLegacyValue(ValueInterface $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withIntValue($fieldDefinition->id, (int) $value->time);
    }

    public function fromLegacyValue(FieldValue $legacyData): ValueInterface {}
}
