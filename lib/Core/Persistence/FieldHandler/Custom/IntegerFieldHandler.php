<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Integer\Value as IntegerValue;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class IntegerFieldHandler implements CustomLegacyFieldHandlerInterface
{
    public function supports(Value $value): bool
    {
        return $value instanceof IntegerValue;
    }

    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * @param \Ibexa\Core\FieldType\Integer\Value $value
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withIntValue($fieldDefinition->id, $value->value);
    }
}
