<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\FieldHandler;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Value;

interface CustomFieldHandlerInterface
{
    /**
     * Checks if given Value can be handled.
     *
     * @param \eZ\Publish\Core\FieldType\Value $value
     *
     * @return bool
     */
    public function supports(Value $value): bool;

    /**
     * Transforms field value object to string.
     *
     * @param \eZ\Publish\Core\FieldType\Value $value
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     *
     * @return string
     */
    public function toString(Value $value, FieldDefinition $fieldDefinition): string;
}
