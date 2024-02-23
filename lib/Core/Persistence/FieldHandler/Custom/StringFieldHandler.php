<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class StringFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(Value $value): bool
    {
        return $value instanceof TextLineValue;
    }

    /**
     * {@inheritDoc}
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return new FieldValue($fieldDefinition->id, $value->text, 0, 0);
    }

    public function fromLegacyValue(FieldValue $legacyData): ValueInterface
    {
        return new TextLineValue($legacyData->getDataText());
    }
}
