<?php

namespace Netgen\Bundle\InformationCollectionBundle\Core\Persistence\Anonymizer\Visitor\Field;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use OutOfBoundsException;

class Aggregate extends FieldAnonymizerVisitor
{
    /**
     * @var \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor[]
     */
    protected $visitors;

    /**
     * Aggregate constructor.
     *
     * @param array $visitors
     */
    public function __construct(array $visitors = [])
    {
        foreach ($visitors as $visitor) {
            $this->addVisitor($visitor);
        }
    }

    /**
     * @param \Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor $visitor
     */
    public function addVisitor(FieldAnonymizerVisitor $visitor)
    {
        $this->visitors[] = $visitor;
    }

    /**
     * @inheritdoc
     */
    public function accept(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function visit(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($ezInfoCollectionAttribute, $contentType)) {
                return $visitor->visit($ezInfoCollectionAttribute, $contentType);
            }
        }

        throw new OutOfBoundsException(
            "No visitor registered for field anonymization"
        );
    }
}
