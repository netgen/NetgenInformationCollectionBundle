<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Legacy;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyHandledFieldValue;

interface LegacyFieldHandlerInterface
{
    /**
     * Checks if given Value can be handled
     *
     * @param Value $value
     *
     * @return boolean
     */
    public function supports(Value $value);

    /**
     * Extract field value as string from Value object
     *
     * @param Value $value
     * @param FieldDefinition $fieldDefinition
     *
     * @return LegacyHandledFieldValue
     */
    public function getValue(Value $value, FieldDefinition $fieldDefinition);

    /**
     * Transforms field value object to string
     *
     * @param Value $value
     * @param FieldDefinition $fieldDefinition
     *
     * @return string
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition);
}