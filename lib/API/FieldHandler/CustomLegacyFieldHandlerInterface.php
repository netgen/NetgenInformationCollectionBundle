<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\FieldHandler;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

interface CustomLegacyFieldHandlerInterface extends CustomFieldHandlerInterface
{
    /**
     * Transforms Ibexa field value to object persistable
     * in legacy information collection database structure.
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue;
}
