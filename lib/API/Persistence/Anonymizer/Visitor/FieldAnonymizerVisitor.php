<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\AttributeValue;

abstract class FieldAnonymizerVisitor
{
    /**
     * Accept the given $ezInfoCollectionAttribute for visiting.
     *
     * @param \Netgen\InformationCollection\API\Value\Attribute $attribute
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return bool
     */
    abstract public function accept(Attribute $attribute, ContentType $contentType): bool;

    /**
     * Visit given $ezInfoCollectionAttribute and return string.
     *
     * @param \Netgen\InformationCollection\API\Value\Attribute $attribute
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \Netgen\InformationCollection\API\Value\AttributeValue
     */
    abstract public function visit(Attribute $attribute, ContentType $contentType): AttributeValue;
}
