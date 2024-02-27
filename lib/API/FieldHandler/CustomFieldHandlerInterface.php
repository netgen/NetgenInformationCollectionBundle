<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\FieldHandler;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

interface CustomFieldHandlerInterface
{
    /**
     * Checks if given Value can be handled.
     */
    public function supports(ValueObject $value): bool;

    /**
     * Transforms field value object to string.
     */
    public function toString(ValueObject $value, FieldDefinition $fieldDefinition): string;
}
