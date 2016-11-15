<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Legacy;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyHandledFieldValue;

class TextLineValueHandler implements LegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof TextLineValue ? true : false;
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
            $value->text
        );
    }

    /**
     * @inheritDoc
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition)
    {
        return '';
    }
}