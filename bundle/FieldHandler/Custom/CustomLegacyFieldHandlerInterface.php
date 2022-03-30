<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

interface CustomLegacyFieldHandlerInterface extends CustomFieldHandlerInterface
{
    /**
     * @param Value $value
     * @param FieldDefinition $fieldDefinition
     *
     * @return LegacyData
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition);

    /**
     * @param LegacyData $legacyData
     * @param FieldDefinition $fieldDefinition
     *
     * @return Value
     */
    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition);
}
