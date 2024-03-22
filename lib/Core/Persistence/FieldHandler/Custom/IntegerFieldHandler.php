<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Integer\Value;
use Ibexa\Core\FieldType\Integer\Value as IntegerValue;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class IntegerFieldHandler implements CustomLegacyFieldHandlerInterface
{
    public function supports(ValueInterface $value): bool
    {
        return $value instanceof IntegerValue;
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
        return FieldValue::withIntValue($fieldDefinition->id, $value->value);
    }

    public function fromLegacyValue(FieldValue $legacyData): IntegerValue
    {
        return new IntegerValue($legacyData->getDataInt());
    }
}
