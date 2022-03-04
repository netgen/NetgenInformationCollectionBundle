<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer\Visitor\Field;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\AttributeValue;

class Simple extends FieldAnonymizerVisitor
{
    /**
     * {@inheritdoc}
     */
    public function accept(Attribute $attribute, ContentType $contentType): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(Attribute $attribute, ContentType $contentType): AttributeValue
    {
        return new AttributeValue(0, 0, 'XXXXXXXXXX');
    }
}
