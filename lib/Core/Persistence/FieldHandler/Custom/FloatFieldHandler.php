<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class FloatFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Value $value): bool
    {
        return $value instanceof FloatValue;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withFloatValue($fieldDefinition->id, $value->value);
    }
}
