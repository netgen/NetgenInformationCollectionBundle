<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class FloatFieldHandler implements CustomLegacyFieldHandlerInterface
{
    public function supports(Value $value): bool
    {
        return $value instanceof FloatValue;
    }

    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * @param \Ibexa\Core\FieldType\Float\Value $value
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withFloatValue($fieldDefinition->id, $value->value);
    }

    public function fromLegacyValue(FieldValue $legacyData): FloatValue
    {
        return new FloatValue($legacyData->getDataFloat());
    }
}
