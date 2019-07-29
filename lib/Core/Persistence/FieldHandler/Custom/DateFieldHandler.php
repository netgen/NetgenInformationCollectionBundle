<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Date\Value as DateValue;
use eZ\Publish\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class DateFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Value $value): bool
    {
        return $value instanceof DateValue;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * @param \eZ\Publish\Core\FieldType\Date\Value $value
     *
     * @return \Netgen\InformationCollection\API\Value\Legacy\FieldValue
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return FieldValue::withIntValue($fieldDefinition->id, $value->date->getTimestamp());
    }
}
