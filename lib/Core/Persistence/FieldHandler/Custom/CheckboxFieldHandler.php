<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class CheckboxFieldHandler implements CustomLegacyFieldHandlerInterface
{
    public function supports(Value $value): bool
    {
        return $value instanceof CheckboxValue;
    }

    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * @param \Ibexa\Core\FieldType\Checkbox\Value $value
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withIntValue($fieldDefinition->id, (int) $value->bool);
    }

    public function fromLegacyValue(FieldValue $legacyData): ValueInterface
    {
        return new CheckboxValue($legacyData->getDataInt() === 1);
    }
}
