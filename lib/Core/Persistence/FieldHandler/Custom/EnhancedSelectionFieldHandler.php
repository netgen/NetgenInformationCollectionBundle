<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class EnhancedSelectionFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(ValueInterface $value): bool
    {
        return $value instanceof EnhancedSelectionValue;
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
        $identifier = '';
        if (isset($value->identifiers[0])) {
            $identifier = $value->identifiers[0];
        }

        return new FieldValue($fieldDefinition->id, $identifier, 0, 0);
    }

    /**
     * {@inheritDoc}
     */
    public function fromLegacyValue(FieldValue $legacyData): ValueInterface
    {
        return new EnhancedSelectionValue([$legacyData->getDataText()]);
    }
}
