<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer\Visitor\Field;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\InformationCollection\API\Value\Attribute;
use Netgen\InformationCollection\API\Value\AttributeValue;
use OutOfBoundsException;

class Aggregate extends FieldAnonymizerVisitor
{
    /**
     * @var \Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor[]
     */
    protected iterable $visitors;

    public function __construct(iterable $visitors)
    {
        $this->visitors = $visitors;
    }

    public function addVisitor(FieldAnonymizerVisitor $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    public function accept(Attribute $attribute, ContentType $contentType): bool
    {
        return true;
    }

    public function visit(Attribute $attribute, ContentType $contentType): AttributeValue
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($attribute, $contentType)) {
                return $visitor->visit($attribute, $contentType);
            }
        }

        throw new OutOfBoundsException(
            'No visitor registered for field anonymization'
        );
    }
}
