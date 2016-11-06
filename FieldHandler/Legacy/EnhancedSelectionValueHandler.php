<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Legacy;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyHandledFieldValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;

class EnhancedSelectionValueHandler implements LegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof EnhancedSelectionValue ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function getValue(Value $value, FieldDefinition $fieldDefinition)
    {
        return new LegacyHandledFieldValue(
            $fieldDefinition->id,
            0,
            0,
            (string)$value
        );
    }
}