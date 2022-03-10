<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Factory;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;
use Netgen\InformationCollection\API\Value\Legacy\FieldValue;

interface FieldValueFactoryInterface
{
    /**
     * Returns value object that represents legacy value.
     *
     * @param \eZ\Publish\Core\FieldType\Value $value
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     *
     * @return \Netgen\InformationCollection\API\Value\Legacy\FieldValue
     */
    public function getLegacyValue(Value $value, FieldDefinition $fieldDefinition): FieldValue;
}
