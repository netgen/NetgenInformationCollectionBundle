<?php

namespace Netgen\Bundle\InformationCollectionBundle\Factory;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

interface LegacyDataFactoryInterface
{
    /**
     * Returns value object that represents legacy value.
     *
     * @param Value $value
     * @param FieldDefinition $fieldDefinition
     *
     * @return LegacyData
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition);

    /**
     * Returns the field value constructed from value object that represents legacy value.
     *
     * @param LegacyData $legacyData
     * @param FieldDefinition $fieldDefinition
     *
     * @return Value
     */
    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition);
}
