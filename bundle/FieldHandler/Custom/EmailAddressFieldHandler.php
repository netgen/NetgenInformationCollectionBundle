<?php

namespace Netgen\Bundle\InformationCollectionBundle\FieldHandler\Custom;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\FieldType\EmailAddress\Value as EmailAddressValue;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class EmailAddressFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value)
    {
        return $value instanceof EmailAddressValue;
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
        return new LegacyData($fieldDefinition->id, 0, 0, $value->email);
    }

    public function fromLegacyValue(LegacyData $legacyData, FieldDefinition $fieldDefinition)
    {
        return new EmailAddressValue($legacyData->getDataText());
    }
}
