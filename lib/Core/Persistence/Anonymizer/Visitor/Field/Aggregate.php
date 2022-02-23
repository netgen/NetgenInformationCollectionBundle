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
    protected $visitors;

    /**
     * Aggregate constructor.
     *
     * @param array $visitors
     */
    public function __construct(iterable $visitors)
    {
        $this->visitors = $visitors;
    }

    /**
     * @param \Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor $visitor
     */
    public function addVisitor(FieldAnonymizerVisitor $visitor)
    {
        $this->visitors[] = $visitor;
    }

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
