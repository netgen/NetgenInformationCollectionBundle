<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;

abstract class FieldAnonymizerVisitor
{
    /**
     * Accept the given $ezInfoCollectionAttribute for visiting.
     *
     * @param \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute $ezInfoCollectionAttribute
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return bool
     */
    abstract public function accept(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType): bool;

    /**
     * Visit given $ezInfoCollectionAttribute and return string.
     *
     * @param \Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute $ezInfoCollectionAttribute
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return string
     */
    abstract public function visit(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType): string;
}
