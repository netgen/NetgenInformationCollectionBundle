<?php

namespace Netgen\InformationCollection\Core\Persistence\FieldHandler\Custom;

use Netgen\InformationCollection\API\FieldHandler\CustomLegacyFieldHandlerInterface;
use Ibexa\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\EmailAddress\Value as EmailAddressValue;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

class EmailAddressFieldHandler implements CustomLegacyFieldHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Value $value): bool
    {
        return $value instanceof EmailAddressValue;
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
        return new FieldValue($fieldDefinition->id, $value->email, 0, 0);
    }

    public function fromLegacyValue(FieldValue $legacyData)
    {
        return new EmailAddressValue($legacyData->getDataText());
    }
}
