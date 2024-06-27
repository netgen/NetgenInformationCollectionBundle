<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\FieldHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

interface CustomLegacyFieldHandlerInterface extends CustomFieldHandlerInterface
{
    /**
     * Transforms Ibexa field value to object persistable
     * in legacy information collection database structure.
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue;

    public function fromLegacyValue(FieldValue $legacyData): ?ValueInterface;
}
