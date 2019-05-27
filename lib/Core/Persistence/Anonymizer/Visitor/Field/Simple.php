<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Persistence\Anonymizer\Visitor\Field;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\InformationCollection\API\Persistence\Anonymizer\Visitor\FieldAnonymizerVisitor;
use Netgen\InformationCollection\Doctrine\Entity\EzInfoCollectionAttribute;

class Simple extends FieldAnonymizerVisitor
{
    /**
     * {@inheritdoc}
     */
    public function accept(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(EzInfoCollectionAttribute $ezInfoCollectionAttribute, ContentType $contentType): string
    {
        return 'XXXXXXXXXX';
    }
}
