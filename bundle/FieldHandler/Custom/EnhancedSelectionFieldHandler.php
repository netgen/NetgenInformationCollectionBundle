<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;

class EnhancedSelectionFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof EnhancedSelectionValue;
    }

    /**
     * @inheritDoc
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition)
    {
        return (string)$value;
    }

    /**
     * @inheritDoc
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition)
    {
        $identifier = '';
        if (isset($value->identifiers[0])) {
            $identifier = $value->identifiers[0];
        }

        return new LegacyData($fieldDefinition->id, 0, 0, $identifier);
    }

    /**
     * @inheritDoc
     */
    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
        return new EnhancedSelectionValue([$legacyData->getDataText()]);
    }
}
