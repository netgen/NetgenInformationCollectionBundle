<?php

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Ibexa\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;

class EnhancedSelectionFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value): bool
    {
        return $value instanceof EnhancedSelectionValue;
    }

    /**
     * @inheritDoc
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition): string
    {
        return (string)$value;
    }

    /**
     * @inheritDoc
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue
    {
        $identifier = '';
        if (isset($value->identifiers[0])) {
            $identifier = $value->identifiers[0];
        }

        return new FieldValue($fieldDefinition->id, $identifier, 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function fromLegacyValue(FieldValue $legacyData)
    {
        return new EnhancedSelectionValue([$legacyData->getDataText()]);
    }

}
