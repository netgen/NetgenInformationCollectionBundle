<?php

namespace Netgen\Bundle\InformationCollectionBundle\API\Persistence\Anonymizer\Visitor;

use Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;

abstract class FieldAnonymizerVisitor
{
    /**
     * Accept the given $ezInfoCollectionAttribute for visiting.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute $ezInfoCollectionAttribute
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return bool
     */
    abstract public function accept(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType);

    /**
     * Visit given $ezInfoCollectionAttribute and return string.
     *
     * @param \Netgen\Bundle\InformationCollectionBundle\Entity\EzInfoCollectionAttribute $ezInfoCollectionAttribute
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return string
     */
    abstract public function visit(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType);
}
