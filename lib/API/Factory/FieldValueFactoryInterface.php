<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Factory;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Value;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

interface FieldValueFactoryInterface
{
    /**
     * Returns value object that represents legacy value.
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue;
}
