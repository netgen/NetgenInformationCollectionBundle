<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\BirthdayBundle\Core\FieldType\Birthday\Value as BirthdayValue;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class BirthdayFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(ValueInterface $value): bool
    {
        return $value instanceof BirthdayValue;
    }

    /**
     * {@inheritDoc}
     */
    public function toString(ValueInterface $value, FieldDefinition $fieldDefinition): string
    {
        return (string) $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getLegacyValue(ValueInterface $value, FieldDefinition $fieldDefinition): FieldValue
    {
        return new FieldValue($fieldDefinition->id, (string) $value, 0, 0);
    }

    /**
     * {@inheritDoc}
     */
    public function fromLegacyValue(FieldValue $legacyData): ValueInterface
    {
        return new BirthdayValue($legacyData->getDataText());
    }
}
